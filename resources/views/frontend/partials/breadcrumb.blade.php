<!--====== Start Hero Section ======-->
<section class="hero-area">
  <div class="breadcrumbs-area bg_cover lazy" @if (!empty($breadcrumb)) data-bg="{{ asset('assets/img/' . $breadcrumb) }}" @endif>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="page-title text-center">
            <h1>{{ !empty($title) ? $title : '' }}</h1>
            <ul class="breadcrumbs-link d-flex justify-content-center">
              <li><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
              <li class="active">{{ !empty($title) ? $title : '' }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!--====== End Hero Section ======-->
