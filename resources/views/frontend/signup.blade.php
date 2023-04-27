@extends('frontend.layout')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->signup_page_title }}
  @endif
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_signup }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_signup }}
  @endif
@endsection

@section('content')
  

  <!--====== Start Signup Area Section ======-->
  <div class="user-area-section pt-120 pb-120">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="user-form">
            <form action="{{ route('user.signup_submit') }}" method="POST">
              @csrf
              <div class="form_group mb-4">
                <label>{{ __('Username') . '*' }}</label>
                <input type="text" class="form_control" name="username" value="{{ old('username') }}">
                @error('username')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

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

              <div class="form_group mb-4">
                <label>{{ __('Confirm Password') . '*' }}</label>
                <input type="password" class="form_control" name="password_confirmation"
                  value="{{ old('password_confirmation') }}">
                @error('password_confirmation')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

              @if ($recaptchaInfo->google_recaptcha_status == 1)
                <div class="form_group my-4">
                  {!! NoCaptcha::renderJs() !!}
                  {!! NoCaptcha::display() !!}

                  @error('g-recaptcha-response')
                    <p class="mt-1 text-danger">{{ $message }}</p>
                  @enderror
                </div>
              @endif

              <div class="form_group">
                <button type="submit" class="main-btn">{{ __('Signup') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--====== End Signup Area Section ======-->
@endsection
