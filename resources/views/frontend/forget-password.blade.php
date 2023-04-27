@extends('frontend.layout')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->forget_password_page_title }}
  @endif
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_forget_password }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_forget_password }}
  @endif
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => $pageHeading ? $pageHeading->forget_password_page_title : '',
  ])

  <!--====== Start Forget Password Area Section ======-->
  <div class="user-area-section pt-120 pb-120">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="user-form">
            <form action="{{ route('user.send_forget_password_mail') }}" method="POST">
              @csrf
              <div class="form_group mb-4">
                <label>{{ __('Email Address') . '*' }}</label>
                <input type="email" class="form_control" name="email" value="{{ old('email') }}">
                @error('email')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

              <div class="form_group">
                <button type="submit" class="main-btn">{{ __('Proceed') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--====== End Forget Password Area Section ======-->
@endsection
