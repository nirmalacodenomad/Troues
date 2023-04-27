@extends('frontend.layout')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->login_page_title }}
  @endif
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_login }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_login }}
  @endif
@endsection

@section('content')
 

  <!--====== Start Login Area Section ======-->
  <div class="user-area-section pt-120 pb-120">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          @isset($digitalProductStatus)
            @if ($digitalProductStatus == 'no')
              <a href="{{ route('shop.checkout', ['checkout_as' => 'guest']) }}"
                class="btn btn-block btn-warning mb-4 py-3 border-0">
                {{ __('Checkout as Guest') }}
              </a>

              <div class="mb-4 text-center">
                <h3><strong>{{ __('OR') }}</strong></h3>
              </div>
            @endif
          @endisset

          @if ($bs->facebook_login_status == 1 || $bs->google_login_status == 1)
            <div class="mb-5">
              <div class="btn-group btn-group-toggle d-flex">
                @if ($bs->facebook_login_status == 1)
                  <a class="btn py-2 facebook-login-btn" href="{{ route('user.login.facebook') }}">
                    <i
                      class="fab fa-facebook-f {{ $currentLanguageInfo->direction == 0 ? 'mr-2' : 'ml-2' }}"></i>{{ __('Login via Facebook') }}
                  </a>
                @endif

                @if ($bs->google_login_status == 1)
                  <a class="btn py-2 google-login-btn" href="{{ route('user.login.google') }}">
                    <i
                      class="fab fa-google {{ $currentLanguageInfo->direction == 0 ? 'mr-2' : 'ml-2' }}"></i>{{ __('Login via Google') }}
                  </a>
                @endif
              </div>
            </div>
          @endif

          <div class="user-form">
            <form action="{{ route('user.login_submit') }}" method="POST">
              @csrf
              <div class="form_group mb-4">
                <label>{{ __('Email Address') . '*' }}</label>
                <input type="email" class="form_control" name="email" value="{{ old('email') }}">
                @error('email')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

              <div class="form_group mb-4">
                <label>{{ __('Password') . '*' }}</label>
                <input type="password" class="form_control" name="password" value="{{ old('password') }}">
                @error('password')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

              @if ($bs->google_recaptcha_status == 1)
                <div class="form_group my-4">
                  {!! NoCaptcha::renderJs() !!}
                  {!! NoCaptcha::display() !!}

                  @error('g-recaptcha-response')
                    <p class="mt-1 text-danger">{{ $message }}</p>
                  @enderror
                </div>
              @endif

              <div class="form_group">
                <button type="submit"
                  class="main-btn {{ $currentLanguageInfo->direction == 1 ? 'ml-4' : 'mr-4' }}">{{ __('Login') }}</button>
                <a href="{{ route('user.forget_password') }}">{{ __('Lost your password?') }}</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--====== End Login Area Section ======-->
@endsection
