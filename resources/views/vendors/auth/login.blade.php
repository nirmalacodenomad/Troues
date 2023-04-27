@extends('frontend.layout')



@section('pageHeading')

  @if (!empty($pageHeading))

    {{ $pageHeading->vendor_login_page_title }}

  @endif

@endsection



@section('metaKeywords')

  @if (!empty($seoInfo))

    {{ $seoInfo->meta_keywords_vendor_login }}

  @endif

@endsection



@section('metaDescription')

  @if (!empty($seoInfo))

    {{ $seoInfo->meta_description_vendor_login }}

  @endif

@endsection



@section('content')





  <!--====== Start Login Area Section ======-->

  <div class="user-area-section pt-120 pb-120">

    <div class="container">

      <div class="row justify-content-center">

        <div class="col-lg-8">

          <div class="user-form">

            <form action="{{ route('vendor.login_submit') }}" method="POST">

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

                <button type="submit" class="main-btn mr-4">{{ __('Login') }}</button>

                <a href="{{ route('vendor.forget.password') }}">{{ __('Lost your password?') }}</a>

              </div>

            </form>

          </div>

        </div>

      </div>

    </div>

  </div>

  <!--====== End Login Area Section ======-->

@endsection

