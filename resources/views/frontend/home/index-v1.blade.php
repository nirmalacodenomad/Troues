

@extends('frontend.layout')



@section('pageHeading')

  {{ __('Home') }}

@endsection



@section('metaKeywords')

  @if (!empty($seoInfo))

    {{ $seoInfo->meta_keyword_home }}

  @endif

@endsection



@section('metaDescription')

  @if (!empty($seoInfo))

    {{ $seoInfo->meta_description_home }}

  @endif

@endsection



@section('content')



<style>

      @media (min-width: 768px){

      .seo-pages-upcxj2-StyledText {

          font-family: RlFreight, HoeflerText-Black, Times New Roman, serif;

          font-size: 80px;

          font-weight: 900;

          letter-spacing: -1px;

          line-height: 90px;

          text-transform: none;

      }

    }

    @media (min-width: 992px){

      .seo-pages-upcxj2-StyledText {

          margin-top: 40px;

      }

    }

    @media (min-width: 768px){

      .seo-pages-upcxj2-StyledText {

          margin-bottom: 16px;

          margin-top: 32px;

      }  

    }

    .seo-pages-upcxj2-StyledText {

    margin: 0;

    color: #121214;

    

}

@media (min-width: 992px){

.seo-pages-7p728d {

    width: 940px;

}}

.seo-pages-7p728d {

    margin-right: auto;

    margin-left: auto;

    padding-top: 0px;

    padding-right: 0px;

    padding-bottom: 0px;

    padding-left: 0px;

    width: 80%;

    position: relative;

}

@media (min-width: 768px){

.seo-pages-14bxsn9 {

    padding-right: 0px;

    padding-left: 0px;

}

}

.seo-pages-14bxsn9 {

    margin-right: auto;

    margin-left: auto;

 

}

@media (min-width: 768px){

.seo-pages-1908avn-StyledText {

    font-family: RlBasisGrotesque, Avenir, Helvetica Neue, Helvetica, sans-serif;

    font-size: 28px;

    font-weight: 900;

    letter-spacing: -0.2px;

    line-height: 32px;

    text-transform: none;

}

}

.seo-pages-1908avn-StyledText {

    margin: 0;

    color: #121214;

}

@media (min-width: 992px){

.seo-pages-1leglqa {

    display: initial;

    left: 10%;

}

}

@media (min-width: 768px){

.seo-pages-1leglqa {

    display: initial;

    bottom: -15px;

    left: -2%;

}

}

.seo-pages-1leglqa {

    background-color: #F2E9FF;

    position: absolute;

    height: 100%;

    width: 100%;

    max-width: 941px;

    z-index: -1;

    bottom: -18px;

}

.testimonial-item-one {

    padding: 28px 27px 45px;

    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px 0 rgb(35 31 32 / 20%), 0 0 1px 0 rgb(35 31 32 / 10%);

    bottom: 10px;

    border-radius: 15px;

    box-shadow: 32px;

}



. {

    opacity: 1;

    width: 5200px;

    transform: translate3d(-1203px, 84px, 0px);

}

  </style>



  <!--====== Start Hero Section ======-->

  @if (count($sliderInfos) > 0)

    <section class="hero-area">

      <div class="hero-slider-one">

        @foreach ($sliderInfos as $sliderInfo)

          <div class="single-hero-slider bg_cover lazy"

            data-bg="{{ asset('assets/img/hero/sliders/' . $sliderInfo->background_image) }}">

            <div class="container">

              <div class="row justify-content-center">

                <div class="col-lg-11">

                  <div class="hero-content text-center">

                    <h1>{{ !empty($sliderInfo->title) ? $sliderInfo->title : '' }}</h1>

                    <p>{{ !empty($sliderInfo->text) ? $sliderInfo->text : '' }}</p>



                    <div class="hero-search-wrapper">

                      <form action="{{ route('all_equipment') }}" method="GET">

                        <div class="row">

                          <div class="col-lg-3">

                            <select class="form_control" name="category">

                              <option selected disabled>{{ __('Search By Category') }}</option>



                              @foreach ($equipCategories as $category)

                                <option value="{{ $category->slug }}">{{ $category->name }}</option>

                              @endforeach

                            </select>

                          </div>



                          <div class="col-lg-3">

                            <div class="form_group">

                              <input type="text" class="form_control"

                                placeholder="{{ __('What are you looking for?') }}" name="keyword">

                            </div>

                          </div>



                          <div class="col-lg-4">

                            <div class="form_group">

                              <div class="input-wrap">

                                <input type="text" class="form_control" id="date-range"

                                  placeholder="{{ __('Search By Date') }}" name="dates" readonly>

                                <i class="far fa-calendar-alt"></i>

                              </div>

                            </div>

                          </div>



                          <div class="col-lg-2">

                            <div class="form_group">

                              <button type="submit" class="search-btn">{{ __('Search') }}</button>

                            </div>

                          </div>

                        </div>

                      </form>

                    </div>

                  </div>

                </div>

              </div>

            </div>

          </div>

        @endforeach

      </div>

    </section>

  @endif

  <!--====== End Hero Section ======-->



   <!--====== Start Drive Section ======-->

  <section  class="drive-area" style="margin-top:20px;">

    <div class="container">

      <p class="seo-pages-upcxj2-StyledText" uid="4f27c359-0830-4d88-b600-126d4a8ca545">Finds all of your production needs</p>

    <div class="seo-pages-7p728d ep71zsp0">

        <div class="seo-pages-14bxsn9 ep71zsp0">

          <h1 class="seo-pages-1908avn-StyledText" uid="8bc50e29-dc74-479a-96f1-04532081ef8a">Explore the world's largest production renting marketplace</h1>

        </div>

        <div class="seo-pages-18i2s0a ep71zsp0">

          <div type="absolute" width="750px" color="brandSurfaceAccent01" class="seo-pages-1leglqa ew8bp2z1">

        </div>

    </div>

  

  </div>

     <div class="row testimonial-slider-one" style="margin-top:80px">

            @foreach ($testimonials as $testimonial)

              <div class="col-lg-4">

                <div class="testimonial-item testimonial-item-one mb-35">
                  <div class="img-card">
                      <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                        alt="">
                  </div>

                  <div class="testimonial-content pt-2" >

                    <!-- <div class="quote">

                      <i class="fal fa-quote-left"></i>

                    </div> -->
                   

                    <p>{{ $testimonial->comment }}</p>

                    <h5>{{ $testimonial->name }}, <span>{{ $testimonial->occupation }}</span></h5>

                  </div>

                </div>

              </div>

            @endforeach

          </div>





  </section>



    <!--====== End Drive Section ======-->



  <!--====== Start About Section ======-->

  @if ($secInfo->about_section_status == 1)

    <section class="about-area pt-70 pb-10">

      <div class="container">

        <div class="row align-items-center">

          <div class="col-lg-7">

            @if (!empty($aboutSectionImage))

              <div class="about-img-box about-img-box-one mb-50">

                <div class="about-img about-img-one">

                  <img data-src="{{ asset('assets/img/' . $aboutSectionImage) }}" alt="about image" class="lazy">

                </div>

              </div>

            @endif

          </div>



          <div class="col-lg-5">

            <div class="about-content-box about-content-box-one mb-50">

              <div class="section-title mb-50">

                <span class="sub-title">{{ !empty($aboutSecInfo->subtitle) ? $aboutSecInfo->subtitle : '' }}</span>

                <h2>{{ !empty($aboutSecInfo->title) ? $aboutSecInfo->title : '' }}</h2>

              </div>

              <p>{!! !empty($aboutSecInfo->text) ? nl2br($aboutSecInfo->text) : '' !!}</p>



              @if (!empty($aboutSecInfo->button_name) && !empty($aboutSecInfo->button_url))

                <a href="{{ $aboutSecInfo->button_url }}" class="main-btn mt-25">{{ $aboutSecInfo->button_name }}</a>

              @endif

            </div>

          </div>

        </div>



        <div class="mt-40 text-center">

          {!! showAd(3) !!}

        </div>

      </div>

    </section>

  @endif

  <!--====== End About Section ======-->



  <!--====== Start Working Process Section ======-->

  @if ($secInfo->work_process_section_status == 1)

    <section class="working-process light-gray pt-70 pb-60">

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-7">

            <div class="section-title text-center mb-85">

              <span

                class="sub-title">{{ !empty($workProcessSecInfo->subtitle) ? $workProcessSecInfo->subtitle : '' }}</span>

              <h2>{{ !empty($workProcessSecInfo->title) ? $workProcessSecInfo->title : '' }}</h2>

              <p>{{ !empty($workProcessSecInfo->text) ? $workProcessSecInfo->text : '' }}</p>

            </div>

          </div>

        </div>



        @if (count($processes) == 0)

          <div class="row text-center">

            <div class="col">

              <h3>{{ __('No Work Process Found') . '!' }}</h3>

            </div>

          </div>

        @else

          <div class="row justify-content-between">

            @foreach ($processes as $process)

              <div class="col-lg-2 col-md-3 process-column">

                <div class="process-item process-item-one mb-30">

                  <div class="count-box">

                    <div class="icon">

                      <i class="{{ $process->icon }}"></i>

                    </div>

                    <div class="process-count">{{ str_pad($process->serial_number, 2, '0', STR_PAD_LEFT) }}</div>

                  </div>



                  <div class="content text-center">

                    <h4>{{ $process->title }}</h4>

                  </div>

                </div>

              </div>

            @endforeach

          </div>

        @endif



        <div class="mt-70 text-center">

          {!! showAd(3) !!}

        </div>

      </div>

    </section>

  @endif

  <!--====== End Working Process Section ======-->



  <!--====== Start Features Section ======-->

  @if ($secInfo->feature_section_status == 1)

    <section class="features-area pt-130 pb-120">

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-7">

            <div class="section-title text-center mb-55">

              <span class="sub-title">{{ !empty($featureSecInfo->subtitle) ? $featureSecInfo->subtitle : '' }}</span>

              <h2>{{ !empty($featureSecInfo->title) ? $featureSecInfo->title : '' }}</h2>

              <p>{{ !empty($featureSecInfo->text) ? $featureSecInfo->text : '' }}</p>

            </div>

          </div>

        </div>



        @if (count($features) == 0)

          <div class="row text-center">

            <div class="col">

              <h3>{{ __('No Feature Found') . '!' }}</h3>

            </div>

          </div>

        @else

          <div class="row">

            @foreach ($features as $feature)

              <div class="col-lg-4 col-md-6 col-sm-12">

                <div class="features-item features-item-one mb-40">

                  <div class="icon">

                    <i class="{{ $feature->icon }}"></i>

                  </div>

                  <div class="content">

                    <h4>{{ $feature->title }}</h4>

                    <p>{{ $feature->text }}</p>

                  </div>

                </div>

              </div>

            @endforeach

          </div>

        @endif



        <div class="mt-50 text-center">

          {!! showAd(3) !!}

        </div>

      </div>

    </section>

  @endif

  <!--====== End Features Section ======-->



  <!--====== Start Counter Section ======-->

  @if ($secInfo->counter_section_status == 1)

    <section class="counter-area bg-with-overlay bg_cover pt-130 pb-90 lazy"

      @if (!empty($counterSectionImage)) data-bg="{{ asset('assets/img/' . $counterSectionImage) }}" @endif>

      <div class="container">

        @if (count($counters) == 0)

          <div class="row text-center">

            <div class="col">

              <h3 class="text-light">{{ __('No Information Found') . '!' }}</h3>

            </div>

          </div>

        @else

          <div class="row">

            @foreach ($counters as $counter)

              <div class="col-lg-3 col-md-6 col-sm-12 counter-column">

                <div class="counter-item counter-item-one mb-40 text-center">

                  <div class="icon">

                    <i class="{{ $counter->icon }}"></i>

                  </div>

                  <div class="content">

                    <h2><span class="count">{{ $counter->amount }}</span>+</h2>

                    <h5>{{ $counter->title }}</h5>

                  </div>

                </div>

              </div>

            @endforeach

          </div>

        @endif

      </div>

    </section>

  @endif

  <!--====== End Counter Section ======-->



  <!--====== Start Featured Equipment Section ======-->

  @if ($secInfo->equipment_section_status == 1)

    <section class="pricing-area pt-20 pb-10">

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-6">

            <div class="section-title text-center mb-50">

              <span

                class="sub-title">{{ !empty($equipmentSecInfo->subtitle) ? $equipmentSecInfo->subtitle : '' }}</span>

              <h2>{{ !empty($equipmentSecInfo->title) ? $equipmentSecInfo->title : '' }}</h2>

              <p>{{ !empty($equipmentSecInfo->text) ? $equipmentSecInfo->text : '' }}</p>

            </div>

          </div>

        </div>



        @if (count($allEquipment) == 0)

          <div class="row text-center">

            <div class="col">

              <h3>{{ __('No Equipment Found') . '!' }}</h3>

            </div>

          </div>

        @else

          <div class="equipment-slider">

            @foreach ($allEquipment as $equipment)

              <div class="slider-item">

                <div class="pricing-item pricing-item-one mb-40">

                  <div class="pricing-img">

                    <!-- <a href="{{ route('equipment_details', ['slug' => $equipment->slug]) }}" class="d-block"> -->
                    <a href="#" class="d-block">

                      <img

                        data-src="{{ asset('assets/img/equipments/thumbnail-images/' . $equipment->thumbnail_image) }}"

                        alt="image" class="lazy" width="500px;" height="250px;">

                    </a>

                  </div>



                  @php

                    $position = $currencyInfo->base_currency_symbol_position;

                    $symbol = $currencyInfo->base_currency_symbol;

                  @endphp



                  <div class="pricing-info">

                    <div class="price-info">

                      <h5>{{ __('Price') }}</h5>



                      @if (!empty($equipment->lowest_price))

                        <span>{{ __('Starts From') }}</span>

                      @endif



                      <div class="price-tag">

                        @if (!empty($equipment->lowest_price))

                          <h4>

                            {{ $position == 'left' ? $symbol : '' }}{{ $equipment->lowest_price }}{{ $position == 'right' ? $symbol : '' }}

                          </h4>

                        @endif

                      </div>

                    </div>



                    <div class="pricing-body">

                      <h5 class="title mb-0">

                        <!-- <a href="{{ route('equipment_details', ['slug' => $equipment->slug]) }}"> -->
                        <a href="#">

                          {{ strlen($equipment->title) > 25 ? mb_substr($equipment->title, 0, 25, 'UTF-8') . '...' : $equipment->title }}

                        </a>

                      </h5>



                      <div class="vendor-name mb-2">

                        @if ($equipment->vendor)

                          {{ __('By') }}

                          <!-- <a href="{{ route('frontend.vendor.details', $equipment->vendor->username) }}"> -->
                          <a href="#">

                            {{ $vendor = optional($equipment->vendor)->username }}

                          </a>

                        @else

                          {{ __('By') }} <a href="javascript:void(0)">{{ __('Admin') }}</a>

                        @endif

                      </div>



                      <div class="price-option">

                        @if (!empty($equipment->per_day_price))

                          <span

                            class="span-btn day">{{ $position == 'left' ? $symbol : '' }}{{ $equipment->per_day_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Day') }}</span>

                        @endif



                        @if (!empty($equipment->per_week_price))

                          <span

                            class="span-btn active-btn week">{{ $position == 'left' ? $symbol : '' }}{{ $equipment->per_week_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Week') }}</span>

                        @endif



                        @if (!empty($equipment->per_month_price))

                          <span

                            class="span-btn month">{{ $position == 'left' ? $symbol : '' }}{{ $equipment->per_month_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Month') }}</span>

                        @endif

                      </div>



                      <!-- <span class="delivary">

                        <a href="#" class="category-search" data-category_slug="{{ $equipment->categorySlug }}">

                          {{ $equipment->categoryName }}

                        </a>

                      </span> -->



                      <!-- <ul class="info-list">

                        @php $features = explode(PHP_EOL, $equipment->features); @endphp



                        @foreach ($features as $feature)

                          @if ($loop->iteration <= 3)

                            <li>{{ $feature }}</li>

                          @else

                            <li class="more-feature d-none">{{ $feature }}</li>

                          @endif

                        @endforeach

                      </ul> -->



                      @if (count($features) > 3)

                        <a href="#" class="mt-2 more-feature-link">{{ __('More Features') . '...' }}</a>

                      @endif

                    </div>



                    <!-- <div class="pricing-bottom">

                      <div class="d-flex flex-row justify-content-center">

                        <div class="rate">

                          <div class="rating" style="width: {{ $equipment->avgRating * 20 . '%;' }}"></div>

                        </div>



                        <div class="{{ $currentLanguageInfo->direction == 0 ? 'ml-3' : 'mr-3' }}">

                          {{ number_format($equipment->avgRating, 2) }}

                          ({{ $equipment->ratingCount . ' ' . __('rating') }})

                        </div>

                      </div>



                      <a href="{{ route('equipment_details', ['slug' => $equipment->slug]) }}" class="main-btn mt-3">

                        {{ __('View') }}

                      </a>

                    </div> -->

                  </div>

                </div>

              </div>

            @endforeach

          </div>

        @endif



        <div class="mt-90 text-center">

          {!! showAd(3) !!}

        </div>

      </div>

    </section>

  @endif

  <!--====== End Featured Equipment Section ======-->



  <!--====== Start Testimonial Section ======-->

  @if ($secInfo->testimonial_section_status == 1)

    <!-- <section class="testimonial-area light-bg pt-130 pb-130">

      <div class="container">

        <div class="row align-items-end">

          <div class="col-lg-6">

            <div class="section-title mb-50">

              <span

                class="sub-title">{{ !empty($testimonialSecInfo->subtitle) ? $testimonialSecInfo->subtitle : '' }}</span>

              <h2>{{ !empty($testimonialSecInfo->title) ? $testimonialSecInfo->title : '' }}</h2>

            </div>

          </div>

        </div>



        @if (count($testimonials) == 0)

          <div class="row text-center">

            <div class="col">

              <h3>{{ __('No Testimonial Found') . '!' }}</h3>

            </div>

          </div>

        @else

          <div class="row testimonial-slider-one">

            @foreach ($testimonials as $testimonial)

              <div class="col-lg-4">

                <div class="testimonial-item testimonial-item-one mb-35">

                  <div class="testimonial-content">

                    <div class="quote">

                      <i class="fal fa-quote-left"></i>

                    </div>

                    <p>{{ $testimonial->comment }}</p>

                    <h5>{{ $testimonial->name }}, <span>{{ $testimonial->occupation }}</span></h5>

                  </div>

                </div>

              </div>

            @endforeach

          </div>

        @endif



        <div class="mt-100 text-center">

          {!! showAd(3) !!}

        </div>

      </div>

    </section> -->

  @endif

  <!--====== End Testimonial Section ======-->



  <!--====== Start Call To Action Section ======-->

  @if ($secInfo->call_to_action_section_status == 1)

    <section class="cta-area bg-with-overlay bg-cover pt-120 pb-130 lazy"

      @if (!empty($callToActionSectionImage)) data-bg="{{ asset('assets/img/' . $callToActionSectionImage) }}" @endif>

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-8">

            <div class="cta-content-box cta-content-box-one content-white text-center">

              <h2>{{ !empty($callToActionSecInfo->subtitle) ? $callToActionSecInfo->subtitle : '' }}</h2>

              <h4>{{ !empty($callToActionSecInfo->title) ? $callToActionSecInfo->title : '' }}</h4>



              @if (!empty($callToActionSecInfo->button_name) && !empty($callToActionSecInfo->button_url))

                <a href="{{ $callToActionSecInfo->button_url }}"

                  class="main-btn">{{ $callToActionSecInfo->button_name }}</a>

              @endif

            </div>

          </div>

        </div>

      </div>

    </section>

  @endif

  <!--====== End Call To Action Section ======-->



  <!--====== Start Blog Section ======-->

  @if ($secInfo->blog_section_status == 1)

    <section class="blog-area pt-130 pb-130">

      <div class="container">

        <div class="row align-items-end">

          <div class="col-lg-6">

            <div class="section-title mb-50">

              <span class="sub-title">{{ !empty($blogSecInfo->subtitle) ? $blogSecInfo->subtitle : '' }}</span>

              <h2>{{ !empty($blogSecInfo->title) ? $blogSecInfo->title : '' }}</h2>

            </div>

          </div>



          <div class="col-lg-6">

            <div class="blog-arrows-one mb-60"></div>

          </div>

        </div>



        @if (count($blogs) == 0)

          <div class="row text-center">

            <div class="col">

              <h3>{{ __('No Blog Found') . '!' }}</h3>

            </div>

          </div>

        @else

          <div class="row blog-slider-one">

            @foreach ($blogs as $blog)

              <div class="col-lg-4">

                <div class="blog-post-item blog-post-item-one">

                  <div class="post-thumbnail">

                    <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}" class="d-block">

                      <img data-src="{{ asset('assets/img/blogs/' . $blog->image) }}" alt="image" class="lazy">

                    </a>

                    <a href="#" class="cat-btn post-category" data-category_slug="{{ $blog->categorySlug }}">

                      {{ $blog->categoryName }}

                    </a>

                  </div>

                  <div class="entry-content">

                    <h3 class="title">

                      <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}">

                        {{ strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'UTF-8') . '...' : $blog->title }}

                      </a>

                    </h3>

                    <div class="post-meta">

                      <ul>

                        <li><span><i class="fas fa-user"></i>{{ $blog->author }}</span></li>

                        <li><span><i

                              class="fas fa-calendar-alt"></i>{{ date_format($blog->created_at, 'M d, Y') }}</span></li>

                      </ul>

                    </div>

                    <p>

                      {{ strlen(strip_tags($blog->content)) > 90 ? mb_substr(strip_tags($blog->content), 0, 90, 'UTF-8') . '...' : $blog->content }}

                    </p>

                    <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}" class="btn-link">

                      {{ __('Read More') }}

                    </a>

                  </div>

                </div>

              </div>

            @endforeach

          </div>

        @endif

      </div>

    </section>

  @endif

  <!--====== End Blog Section ======-->



  <!--====== Start Partner/Sponsor Section ======-->

  @if ($secInfo->partner_section_status == 1)

    <section

      class="sponsor-area @if ($secInfo->blog_section_status == 0) pt-130 @endif @if ($secInfo->subscribe_section_status == 0) pb-130 @endif">

      <div class="container">

        @if (count($partners) == 0)

          <div class="row text-center">

            <div class="col">

              <h3>{{ __('No Partner Information Found') . '!' }}</h3>

            </div>

          </div>

        @else

          <div class="sponsor-slider-one">

            @foreach ($partners as $partner)

              <div class="sponsor-item sponsor-item-one mb-40">

                <a href="{{ $partner->url }}" target="_blank">

                  <img data-src="{{ asset('assets/img/partners/' . $partner->image) }}" alt="sponsor image"

                    class="lazy">

                </a>

              </div>

            @endforeach

          </div>

        @endif



        <div class="mt-100 text-center">

          {!! showAd(3) !!}

        </div>

      </div>

    </section>

  @endif

  <!--====== End Partner/Sponsor Section ======-->



  <!--====== Start Newsletter Section ======-->

  @if ($secInfo->subscribe_section_status == 1)

    <section class="newsletter-area @if ($secInfo->partner_section_status == 1) pt-130 @endif">

      <div class="container">

        <div class="newsletter-wrapper-one">

          <div class="row justify-content-center">

            <div class="col-lg-8">

              <div class="newsletter-content-box">

                <div class="section-title text-center mb-30">

                  <h2>{{ __('Subscribe to Our Newsletter') }}</h2>

                </div>



                <form class="newsletter-form subscription-form" action="{{ route('store_subscriber') }}"

                  method="POST">

                  @csrf

                  <div class="form_group">

                    <input type="email" class="form_control" placeholder="{{ __('Enter Your Email Address') }}"

                      name="email_id">

                    <button type="submit" class="newsletter-btn">{{ __('Subscribe') }}</button>

                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

      </div>

    </section>

  @endif

  <!--====== End Newsletter Section ======-->



  {{-- equipment search form start --}}

  <form class="d-none" action="{{ route('all_equipment') }}" method="GET">

    <input type="hidden" id="category-id" name="category">



    <button type="submit" id="submitBtn"></button>

  </form>

  {{-- equipment search form end --}}



  {{-- post search form start --}}

  <form class="d-none" action="{{ route('blog') }}" method="GET">

    <input type="hidden" id="categoryKey" name="category">



    <button type="submit" id="form-submit-btn"></button>

  </form>

  {{-- post search form end --}}

@endsection



@section('script')

  <script type="text/javascript" src="{{ asset('assets/js/equipment.js') }}"></script>

@endsection

