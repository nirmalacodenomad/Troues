@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl-style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Page Headings') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Basic Settings') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Page Headings') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <form
          action="{{ route('admin.basic_settings.update_page_headings', ['language' => request()->input('language')]) }}"
          method="post">
          @csrf
          <div class="card-header">
            <div class="row">
              <div class="col-lg-10">
                <div class="card-title">{{ __('Update Page Headings') }}</div>
              </div>

              <div class="col-lg-2">
                @includeIf('backend.partials.languages')
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
                <div class="form-group">
                  <label>{{ __('Blog Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="blog_page_title"
                    value="{{ $data != null ? $data->blog_page_title : '' }}">
                  @error('blog_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Blog Details Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="blog_details_page_title"
                    value="{{ $data != null ? $data->blog_details_page_title : '' }}">
                  @error('blog_details_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Contact Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="contact_page_title"
                    value="{{ $data != null ? $data->contact_page_title : '' }}">
                  @error('contact_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Products Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="products_page_title"
                    value="{{ $data != null ? $data->products_page_title : '' }}">
                  @error('products_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Products Details Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="product_details_page_title"
                    value="{{ $data != null ? $data->product_details_page_title : '' }}">
                  @error('product_details_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Error Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="error_page_title"
                    value="{{ $data != null ? $data->error_page_title : '' }}">
                  @error('error_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('FAQ Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="faq_page_title"
                    value="{{ $data != null ? $data->faq_page_title : '' }}">
                  @error('faq_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Forget Password Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="forget_password_page_title"
                    value="{{ $data != null ? $data->forget_password_page_title : '' }}">
                  @error('forget_password_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Login Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="login_page_title"
                    value="{{ $data != null ? $data->login_page_title : '' }}">
                  @error('login_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Signup Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="signup_page_title"
                    value="{{ $data != null ? $data->signup_page_title : '' }}">
                  @error('signup_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="form-group">
                  <label>{{ __('Vendor Login Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="vendor_login_page_title"
                    value="{{ $data != null ? $data->vendor_login_page_title : '' }}">
                  @error('vendor_login_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="form-group">
                  <label>{{ __('Vendor Forget Password Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="vendor_forget_password_page_title"
                    value="{{ $data != null ? $data->vendor_forget_password_page_title : '' }}">
                  @error('vendor_forget_password_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Vendor Signup Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="vendor_signup_page_title"
                    value="{{ $data != null ? $data->vendor_signup_page_title : '' }}">
                  @error('vendor_signup_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Cart Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="cart_page_title"
                    value="{{ $data != null ? $data->cart_page_title : '' }}">
                  @error('cart_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Checkout Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="checkout_page_title"
                    value="{{ $data != null ? $data->checkout_page_title : '' }}">
                  @error('checkout_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Equipment Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="equipment_page_title"
                    value="{{ $data != null ? $data->equipment_page_title : '' }}">
                  @error('equipment_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label>{{ __('Equipment Details Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="equipment_details_page_title"
                    value="{{ $data != null ? $data->equipment_details_page_title : '' }}">
                  @error('equipment_details_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="form-group">
                  <label>{{ __('Vendor Page Title') . '*' }}</label>
                  <input type="text" class="form-control" name="vendor_page_title"
                    value="{{ $data != null ? $data->vendor_page_title : '' }}">
                  @error('vendor_page_title')
                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer">
            <div class="row">
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-success">
                  {{ __('Update') }}
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
