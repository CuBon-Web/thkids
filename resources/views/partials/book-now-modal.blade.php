<div class="modal fade book-now-modal" id="bookNowModal" tabindex="-1" aria-labelledby="bookNowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <div>
          <div class="h4 modal-title mb-1" id="bookNowModalLabel">Book Now</div>
          <p class="mb-0 text-muted small">Leave your details and we will contact you shortly.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-3">
        @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif
        @if(isset($errors) && $errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form action="{{ route('postcontact') }}" method="post" class="book-now-form">
          @csrf
          <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
          <input type="hidden" name="book_now_modal" value="1">
          <input type="hidden" name="mess" value="Book Now request from website header">
          <div class="mb-3">
            <label for="book_now_name" class="form-label">Full Name</label>
            <input
              type="text"
              class="form-control"
              id="book_now_name"
              name="name"
              placeholder="Enter your name"
              value="{{ old('name') }}"
              required
              autocomplete="name"
            >
          </div>
          <div class="mb-3">
            <label for="book_now_email" class="form-label">Email</label>
            <input
              type="email"
              class="form-control"
              id="book_now_email"
              name="email"
              placeholder="Enter your email"
              value="{{ old('email') }}"
              required
              autocomplete="email"
            >
          </div>
          <div class="mb-4">
            <label for="book_now_phone" class="form-label">Phone</label>
            <input
              type="tel"
              class="form-control"
              id="book_now_phone"
              name="phone"
              placeholder="Enter your phone number"
              value="{{ old('phone') }}"
              required
              autocomplete="tel"
            >
          </div>
          <button type="submit" class="theme-btn btn-style-one w-100">
            <span class="btn-title">Submit</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

@if(session('open_book_now_modal') || (isset($errors) && $errors->any()))
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('bookNowModal');
    if (modalEl && window.bootstrap && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
  });
</script>
@endif
