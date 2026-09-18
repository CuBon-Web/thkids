<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\product\Product;
use Cart,Auth,Redirect,DB;
use App\models\Bill\BillDetail;
use App\models\Bill\Bill;
use Mail;
use App\Mail\DemoMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\models\website\Setting;

class CartController extends Controller
{
    private function getNotificationEmails()
    {
        $setting = Setting::first(['email']);
        if (!$setting || empty($setting->email)) {
            return [];
        }

        return collect(explode(',', (string)$setting->email))
            ->map(function ($email) {
                return trim($email);
            })
            ->filter(function ($email) {
                return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL);
            })
            ->values()
            ->all();
    }

    private function sendBillNotificationMail(Bill $bill, array $cart)
    {
        $emails = $this->getNotificationEmails();
        if (empty($emails)) {
            Log::warning('Order notification skipped: settings.email is empty or invalid', [
                'code_bill' => $bill->code_bill,
            ]);
            return;
        }

        $data = ['cus' => $bill, 'bill' => $cart];
        Mail::to($emails)->send(new DemoMail($data));
    }

    private function getCartLinePrice(array $item)
    {
        if ((int) ($item['status_variant'] ?? 0) === 1) {
            return (float) ($item['price'] ?? 0);
        }
        if ((float) ($item['discount'] ?? 0) > 0) {
            return (float) $item['discount'];
        }
        return (float) ($item['price'] ?? 0);
    }

    private function calculateCartTotal(array $cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $this->getCartLinePrice($item) * (int) ($item['quantity'] ?? 1);
        }
        return $total;
    }

    private function cartResponse(array $cart, $extra = [])
    {
        return response()->json(array_merge([
            'success' => true,
            'cart_count' => collect($cart)->sum('quantity'),
            'total' => $this->calculateCartTotal($cart),
        ], $extra));
    }

    private function reduceProductQtyByBill(Bill $bill)
    {
        $billDetails = BillDetail::where('code_bill', $bill->code_bill)->get();
        foreach ($billDetails as $detail) {
            $product = Product::find($detail->code_product);
            if (!$product) continue;
            if ($product->qty > $detail->qty) {
                $product->qty = $product->qty - $detail->qty;
            } else {
                $product->qty = 0;
            }
            $product->save();
        }
    }

    private function buildPayosSignature(array $data, $checksumKey)
    {
        ksort($data);
        $pairs = [];
        foreach ($data as $key => $value) {
            $pairs[] = $key . '=' . $value;
        }
        return hash_hmac('sha256', implode('&', $pairs), $checksumKey);
    }

    private function createPayosPaymentLink(Bill $bill, array $cart, Request $request)
    {
        $clientId = env('PAYOS_CLIENT_ID');
        $apiKey = env('PAYOS_API_KEY');
        $checksumKey = env('PAYOS_CHECKSUM_KEY');
        if (!$clientId || !$apiKey || !$checksumKey) {
            throw new \Exception('Thiếu cấu hình PAYOS_CLIENT_ID/PAYOS_API_KEY/PAYOS_CHECKSUM_KEY');
        }

        $orderCode = (int)$bill->code_bill;
        $description = 'DH' . $orderCode;
        $returnUrl = route('payos.return');
        $cancelUrl = route('payos.cancel');

        $items = [];
        foreach ($cart as $item) {
            $items[] = [
                'name' => mb_substr($item['name'], 0, 25),
                'quantity' => (int)($item['quantity'] ?? 1),
                'price' => $this->getCartLinePrice($item),
            ];
        }

        $signatureData = [
            'amount' => (int)$bill->total_money,
            'cancelUrl' => $cancelUrl,
            'description' => $description,
            'orderCode' => $orderCode,
            'returnUrl' => $returnUrl,
        ];

        $payload = [
            'orderCode' => $orderCode,
            'amount' => (int)$bill->total_money,
            'description' => $description,
            'items' => $items,
            'returnUrl' => $returnUrl,
            'cancelUrl' => $cancelUrl,
            'buyerName' => $bill->cus_name,
            'buyerEmail' => $bill->cus_email,
            'buyerPhone' => $bill->cus_phone,
            'buyerAddress' => $bill->cus_address,
            'signature' => $this->buildPayosSignature($signatureData, $checksumKey),
        ];

        $response = Http::withHeaders([
            'x-client-id' => $clientId,
            'x-api-key' => $apiKey,
        ])->post('https://api-merchant.payos.vn/v2/payment-requests', $payload);

        if (!$response->ok()) {
            Log::error('PayOS create payment link failed', ['response' => $response->body()]);
            throw new \Exception('Không tạo được link thanh toán PayOS');
        }

        $json = $response->json();
        if (!isset($json['data']['checkoutUrl'])) {
            Log::error('PayOS checkoutUrl missing', ['response' => $json]);
            throw new \Exception('PayOS không trả về checkoutUrl');
        }
        return $json['data']['checkoutUrl'];
    }

    private function markBillPaidByOrderCode($orderCode)
    {
        $bill = Bill::where('code_bill', $orderCode)->first();
        if (!$bill) return null;
        if ((int)$bill->statu !== 1) {
            $bill->statu = 1;
            $bill->save();
            $this->reduceProductQtyByBill($bill);
            $billDetails = BillDetail::where('code_bill', $bill->code_bill)->get()->toArray();
            $this->sendBillNotificationMail($bill, $billDetails);
        }
        return $bill;
    }

    public function checkout(){
            $data['cart'] = session()->get('cart', []);
            $data['profile'] = Auth::guard('customer')->user();
            return view('cart.checkout',$data);
        
    }
    public function postBill(Request $request){
        $profile = Auth::guard('customer')->user();
        $cart = session()->get('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('listCart')->with('error', 'Giỏ hàng đang trống');
        }

        $code_bill = (int)(date('ymdHis') . rand(10, 99));
        $paymentMethod = $request->payment_method === 'online' ? 'online' : 'cod';
        DB::beginTransaction();
			try {
				$query = new Bill();
				$query->code_bill = $code_bill;
				$query->code_customer = $profile ? $profile->id : 0;
				$query->total_money = $request->total_money;
				$query->statu = 0;
				$query->note = $request->note;
                $query->payment_method = $paymentMethod;
                $query->cus_name = $request->billingName;
                $query->cus_phone = $request->billingPhone;
                $query->cus_email= $request->billingEmail;
                $query->cus_address= $request->billingAddress;
                $query->transport_price = $request->shippingMethod ? $request->shippingMethod : 0;
				$query->save();
                
					
                foreach($cart as $key => $item){
                    $billdetail = new BillDetail();
                    $billdetail->code_bill = $code_bill;
                    $billdetail->code_product = $item['idpro'];
                    $billdetail->name =$item['name'];
                    $billdetail->price = $this->getCartLinePrice($item);
                    $billdetail->qty = $item['quantity'];
                    $billdetail->images = $item['image'];
                    $billdetail->variant = $item['status_variant'] == 1 ? $item['variant'] : '';
                    $billdetail->save();
                }

                if ($paymentMethod === 'online') {
                    $checkoutUrl = $this->createPayosPaymentLink($query, $cart, $request);
                    DB::commit();
                    return redirect()->away($checkoutUrl);
                }

                $this->reduceProductQtyByBill($query);
                DB::commit();
                $this->sendBillNotificationMail($query, $cart);
                $request->session()->forget('cart');
                return view('cart.orderSuccess');
			} catch (\Throwable $e) {
			    DB::rollBack();
                Log::error('Checkout failed', ['error' => $e->getMessage()]);
                return back()->with('error','Gửi đơn hàng thất bại: ' . $e->getMessage());
			}
    }

    public function payosReturn(Request $request)
    {
        $orderCode = (int)$request->get('orderCode', 0);
        $status = strtoupper((string)$request->get('status', ''));
        if ($orderCode > 0 && $status === 'PAID') {
            $bill = $this->markBillPaidByOrderCode($orderCode);
            if ($bill) {
                session()->forget('cart');
                return view('cart.orderSuccess');
            }
        }
        return redirect()->route('checkout')->with('error', 'Thanh toán chưa hoàn tất, vui lòng thử lại.');
    }

    public function payosCancel(Request $request)
    {
        $orderCode = (int)$request->get('orderCode', 0);
        if ($orderCode > 0) {
            $bill = Bill::where('code_bill', $orderCode)->first();
            if ($bill && (int)$bill->statu === 0) {
                $bill->statu = 2;
                $bill->save();
            }
        }
        return redirect()->route('checkout')->with('error', 'Bạn đã hủy thanh toán online.');
    }

    public function payosWebhook(Request $request)
    {
        try {
            $data = $request->input('data', []);
            $code = $request->input('code');
            if ((string)$code === '00' && isset($data['orderCode'])) {
                $this->markBillPaidByOrderCode((int)$data['orderCode']);
            }
        } catch (\Throwable $e) {
            Log::error('PayOS webhook error', ['error' => $e->getMessage()]);
        }
        return response()->json(['error' => 0, 'message' => 'ok']);
    }

    public function listCart(){
        $cart = session()->get('cart', []);
        $productUrls = [];

        if (!empty($cart)) {
            $ids = collect($cart)->pluck('idpro')->unique()->filter()->values();
            $products = Product::select('id', 'slug', 'cate_slug', 'type_slug')
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');

            foreach ($products as $id => $product) {
                $productUrls[$id] = route('detailProduct', [
                    'cate' => $product->cate_slug,
                    'type' => $product->type_slug ?: 'loai',
                    'id' => $product->slug,
                ]);
            }
        }

        return view('cart.list', [
            'cart' => $cart,
            'productUrls' => $productUrls,
        ]);
    }
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        $price = (float) $request->input('price', 0);
        if ($price <= 0) {
            $price = (float) ($product->discount > 0 ? $product->discount : $product->price);
        }

        $images = json_decode($product->images, true) ?? [];
        $image = $images[0] ?? '';
        $productName = function_exists('languageName')
            ? languageName($product->name)
            : (string) $product->name;

        $cartKey = (string) $request->product_id;
        $variant = trim((string) $request->input('variant', ''));
        if ((int) $product->status_variant === 1 && $variant !== '') {
            $cartKey = $request->product_id . '_' . md5($variant);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = (int) $cart[$cartKey]['quantity'] + (int) $request->quantity;
            $cart[$cartKey]['price'] = $price;
            $cart[$cartKey]['variant'] = $variant;
        } else {
            $cart[$cartKey] = [
                'idpro' => (int) $request->product_id,
                'cart_key' => $cartKey,
                'name' => $productName,
                'variant' => $variant,
                'quantity' => (int) $request->quantity,
                'price' => $price,
                'status_variant' => (int) $product->status_variant,
                'discount' => (float) $product->discount,
                'image' => $image,
                'slug' => $product->slug,
                'cate_slug' => $product->cate_slug,
                'type_slug' => $product->type_slug,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => (int) collect($cart)->sum('quantity'),
            'redirect_cart' => route('listCart'),
            'redirect_checkout' => route('checkout'),
        ]);
    }
    public function update(Request $request)
    {
        $cart = session()->get('cart', []);
        $key = (string) $request->id;
        if ($key === '' || !isset($cart[$key])) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ'], 404);
        }

        $quantity = max(1, (int) $request->quantity);
        $cart[$key]['quantity'] = $quantity;
        session()->put('cart', $cart);

        $linePrice = $this->getCartLinePrice($cart[$key]);
        return $this->cartResponse($cart, [
            'line_total' => $linePrice * $quantity,
            'quantity' => $quantity,
        ]);
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        $key = (string) $request->id;
        if ($key !== '' && isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return $this->cartResponse($cart);
    }

    public function clearCart()
    {
        session()->forget('cart');
        return $this->cartResponse([]);
    }
    public function orderSuccess()
    {
        return view('cart.orderSuccess');
    }
}
