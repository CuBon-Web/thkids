<div id="quick-view-product" class="quickview-product" style="display:none;"
    data-add-cart-url="{{ route('add.to.cart') }}"
    data-checkout-url="{{ route('checkout') }}"
    data-cart-url="{{ route('listCart') }}">
    <div class="quickview-overlay fancybox-overlay fancybox-overlay-fixed"></div>
    <div class="quick-view-product"></div>
    <div id="quickview-modal" style="display:none;">
        <div class="block-quickview primary_block details-product">
            <div class="row">
                <div class="product-left-column product-images col-xs-12 col-sm-4 col-md-4 col-lg-5 col-xl-5">
                    <div class="image-block large-image col_large_default">
                        <span class="view_full_size">
                            <a class="img-product" title="" href="javascript:;">
                                <img src="{{ asset('frontend/images/lazy.png') }}" id="product-featured-image-quickview" class="img-responsive product-featured-image-quickview" alt="quickview" />
                            </a>
                        </span>
                        <div class="loading-imgquickview" style="display:none;"></div>
                    </div>
                    <div class="more-view-wrapper clearfix d-none">
                        <div class="thumbs_quickview owl_nav_custome1 swiper-container" id="thumbs_list_quickview">
                            <ul class="product-photo-thumbs quickview-more-views-owlslider not-thuongdq swiper-wrapper" id="thumblist_quickview"></ul>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>
                </div>
                <div class="product-center-column product-info product-item col-xs-12 col-sm-6 col-md-8 col-lg-7 col-xl-7 details-pro style_product style_border">
                    <div class="head-qv group-status">
                        <h3 class="qwp-name title-product"></h3>
                        <div class="vend-qv group-status">
                            <div class="left_vend">
                                <div class="first_status">Tình trạng:
                                    <span class="soluong status_name">Còn hàng</span>
                                </div>
                                <div class="top_sku first_status">Mã sản phẩm:
                                    <span class="sku_ status_name"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="quickview-info">
                        <span class="prices price-box">
                            <span class="price product-price"></span>
                            <del class="old-price"></del>
                        </span>
                    </div>
                    <form action="/cart/add" method="post" enctype="multipart/form-data" class="quick_option variants form-ajaxtocart form-product qv-form">
                        <div id="qv-variant-wrap" class="qv-attributes-wrap"></div>
                        <div class="form_product_content">
                            <div class="count_btn_style quantity_wanted_p">
                                <div class="soluong1 clearfix">
                                    <span class="soluong_h">Số lượng:</span>
                                    <div class="sssssscustom input_number_product">
                                        <button type="button" class="btn_num num_1 button button_qty qv-qty-minus">-</button>
                                        <input type="text" name="quantity" value="1" maxlength="3" class="form-control prd_quantity qv-quantity">
                                        <button type="button" class="btn_num num_2 button button_qty qv-qty-plus">+</button>
                                    </div>
                                </div>
                                <div class="button_actions clearfix qv-button-actions">
                                    <button type="button" class="btn_cool btn btn_base qv-add-cart">
                                        <span class="btn-content text_1">Thêm vào giỏ hàng</span>
                                    </button>
                                    <button type="button" class="btn_cool btn btn_base qv-buy-now">
                                        <span class="btn-content text_1">Mua ngay</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="product-description product-summary">
                        <div class="rte"></div>
                    </div>
                </div>
            </div>
        </div>
        <a title="Close" class="quickview-close close-window" href="javascript:;">
            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-times fa-w-10"><path fill="currentColor" d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg>
        </a>
    </div>
</div>
<style>
    #quick-view-product > .quickview-overlay {
        z-index: 1;
    }
    #quick-view-product > .quick-view-product {
        z-index: 2;
        pointer-events: auto;
    }
    #quick-view-product .qv-button-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        position: relative;
        z-index: 5;
    }
    #quick-view-product .qv-button-actions .btn_cool {
        flex: 1 1 calc(50% - 5px);
        min-width: 140px;
        cursor: pointer;
        pointer-events: auto;
    }
    #quick-view-product .qv-buy-now {
        background: #c60d00 !important;
    }
    #quick-view-product .qv-buy-now:hover {
        background: #a00b00 !important;
    }
    #quick-view-product .qv-add-cart.is-loading,
    #quick-view-product .qv-buy-now.is-loading {
        opacity: 0.7;
        pointer-events: none;
    }
    #quick-view-product .qv-attributes-wrap:empty {
        display: none;
    }
    #quick-view-product .qv-form {
        display: block !important;
        margin: 12px 0;
        position: relative;
        z-index: 3;
    }
    #quick-view-product .quantity_wanted_p,
    #quick-view-product .qv-button-actions {
        display: block !important;
    }
    #quick-view-product .product-description.product-summary {
        max-height: 120px;
        overflow-y: auto;
    }
    .qv-cart-notify {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 10050;
        background: #28a745;
        color: #fff;
        padding: 12px 20px;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        opacity: 0;
        transform: translateY(-10px);
        transition: all .25s ease;
        pointer-events: none;
    }
    .qv-cart-notify.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>
<div id="qv-cart-notify" class="qv-cart-notify" aria-live="polite"></div>
