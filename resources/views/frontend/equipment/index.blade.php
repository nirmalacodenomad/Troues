@extends('frontend.layout')



@section('pageHeading')

  @if (!empty($pageHeading))

    {{ $pageHeading ? $pageHeading->equipment_page_title : '' }}

  @endif

@endsection



@section('metaKeywords')

  @if (!empty($seoInfo))

    {{ $seoInfo ? $seoInfo->meta_keyword_equipment : '' }}

  @endif

@endsection



@section('metaDescription')

  @if (!empty($seoInfo))

    {{ $seoInfo->meta_description_equipment }}

  @endif

@endsection



@section('content')




  <!--====== Start Equipments section ======-->

  <section class="pricing-area pricing-list-section pt-60 pb-70">

    <div class="container">

      <div class="equipments-search-filter mb-60">

        <div class="search-filter-form">

          <form action="{{ route('all_equipment') }}" method="GET">

            <div class="row">

              <div class="col-lg-3 col-md-6 col-sm-12">

                <div class="form_group">

                  <input type="text" name="keyword" class="form_control"

                    placeholder="{{ __('What are you looking for?') }}"

                    value="{{ !empty(request()->input('keyword')) ? request()->input('keyword') : '' }}"

                    id="keyword-search">

                </div>

              </div>



              <div class="col-lg-3 col-md-6 col-sm-12">

                <div class="form_group">

                  <select class="form_control" name="sort" id="sort-search">

                    <option selected disabled>{{ __('Sort By') }}</option>

                    <option {{ request()->input('sort') == 'new' ? 'selected' : '' }} value="new">

                      {{ __('New Equipment') }}

                    </option>

                    <option {{ request()->input('sort') == 'old' ? 'selected' : '' }} value="old">

                      {{ __('Old Equipment') }}

                    </option>

                    <option {{ request()->input('sort') == 'ascending' ? 'selected' : '' }} value="ascending">

                      {{ __('Price') . ': ' . __('Ascending') }}

                    </option>

                    <option {{ request()->input('sort') == 'descending' ? 'selected' : '' }} value="descending">

                      {{ __('Price') . ': ' . __('Descending') }}

                    </option>

                  </select>

                </div>

              </div>



              <div class="col-lg-3 {{ $currentLanguageInfo->direction == 1 ? 'offset-lg-1' : '' }} col-md-6 col-sm-12">

                <div class="form_group">

                  <div class="input-wrap">

                    <input type="text" name="dates" class="form_control" id="date-range"

                      placeholder="{{ __('Search By Date') }}"

                      value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}" readonly>

                    <i class="far fa-calendar-alt"></i>

                  </div>

                </div>

              </div>



              <div class="col-lg-2 {{ $currentLanguageInfo->direction == 0 ? 'offset-lg-1' : '' }} col-md-6 col-sm-12">

                <div class="form_group">

                  <button type="submit" class="search-btn">{{ __('Search') }}</button>

                </div>

              </div>

            </div>

          </form>

        </div>

      </div>



      <div class="row">

        <div class="col-lg-3">

          <div class="sidebar-widget-area">

            @if (count($categories) > 0)

              <div class="widget equipment-categories mb-50">

                <h4 class="widget-title">{{ __('Categories') }}</h4>

                <ul class="list">

                  <li>

                    <a href="#" class="category-search {{ empty(request()->input('category')) ? 'active' : '' }}">

                      {{ __('All') }}

                    </a>

                  </li>



                  @foreach ($categories as $category)

                    <li>

                      <a href="#"

                        class="category-search {{ $category->slug == request()->input('category') ? 'active' : '' }}"

                        data-category_slug="{{ $category->slug }}">

                        {{ $category->name }}

                      </a>

                    </li>

                  @endforeach

                </ul>

              </div>

            @endif



            <!-- <div class="widget price-range-widget mb-50">

              <h4 class="widget-title">{{ __('Filter Price') }}</h4>

              <div id="range-slider" class="mb-20"></div>

              <div class="price-number d-flex">

                <span class="text">{{ __('Price') . ' :' }}</span>

                <span class="amount"><input type="text" id="amount" readonly></span>

              </div>

            </div> -->



            <div class="equipment-advertise mb-50">

              {!! showAd(2) !!}

            </div>

          </div>

        </div>



        <div class="col-lg-9">

          <div class="equipments-list-wrapper">

            @if (count($allEquipment) == 0)

              <div class="row text-center mt-5">

                <div class="col">

                  <h4>{{ __('No Equipment Found') . '!' }}</h4>

                </div>

              </div>

            @else

              @foreach ($allEquipment as $equipment)

                <div class="pricing-item pricing-item-three mb-60">

                  <div class="pricing-img">

                    <a href="{{ route('equipment_details', ['slug' => $equipment->slug]) }}" class="d-block">

                      <img

                        data-src="{{ asset('assets/img/equipments/thumbnail-images/' . $equipment->thumbnail_image) }}"

                        alt="image" class="lazy bg-img">

                    </a>



                    @if (!empty($equipment->offer))

                      <!-- <span class="discount">{{ $equipment->offer . '% ' . __('off') }}</span> -->

                    @endif

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

                      <h3 class="title mb-0">

                        <a href="{{ route('equipment_details', ['slug' => $equipment->slug]) }}">

                          {{ strlen($equipment->title) > 25 ? mb_substr($equipment->title, 0, 25, 'UTF-8') . '...' : $equipment->title }}

                        </a>



                      </h3>

                      <div class="vendor-name">

                        @if ($equipment->vendor)

                          {{ __('By') }}

                          <a href="{{ route('frontend.vendor.details', $equipment->vendor->username) }}">

                            {{ $vendor = optional($equipment->vendor)->username }}

                          </a>

                        @else

                          {{ __('By') }} {{ __('Admin') }}

                        @endif

                      </div>

                      <div class="price-option">

                        @if (!empty($equipment->per_day_price))

                          <span

                            class="span-btn day">{{ $position == 'left' ? $symbol : '' }}{{ $equipment->per_day_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Day') }}</span>

                        @endif



                        @if (!empty($equipment->per_week_price))

                          <span

                            class="span-btn week">{{ $position == 'left' ? $symbol : '' }}{{ $equipment->per_week_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Week') }}</span>

                        @endif



                        @if (!empty($equipment->per_month_price))

                          <span

                            class="span-btn month">{{ $position == 'left' ? $symbol : '' }}{{ $equipment->per_month_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Month') }}</span>

                        @endif

                      </div>



                      <ul class="info-list">

                        @php $features = explode(PHP_EOL, $equipment->features); @endphp



                        @foreach ($features as $feature)

                          @if ($loop->iteration <= 4)

                            <li>{{ $feature }}</li>

                          @endif

                        @endforeach

                      </ul>

                    </div>



                    <!-- <div class="pricing-bottom d-flex align-items-center justify-content-between">

                      <div class="d-flex flex-row">

                        <div class="rate">

                          <div class="rating" style="width: {{ $equipment->avgRating * 20 . '%;' }}"></div>

                        </div>

                        <span

                          class="{{ $currentLanguageInfo->direction == 0 ? 'ml-3' : 'mr-3' }}">{{ number_format($equipment->avgRating, 2) }}

                          ({{ $equipment->ratingCount . ' ' . __('rating') }})

                        </span>

                      </div>



                      <a href="{{ route('equipment_details', ['slug' => $equipment->slug]) }}"

                        class="main-btn">{{ __('View') }}</a>

                    </div> -->

                  </div>

                </div>

              @endforeach



              {{ $allEquipment->appends([

                      'keyword' => request()->input('keyword'),

                      'sort' => request()->input('sort'),

                      'category' => request()->input('category'),

                      'min' => request()->input('min'),

                      'max' => request()->input('max'),

                      'dates' => request()->input('dates'),

                  ])->links() }}

            @endif

          </div>



          <div class="text-center mt-55">

            {!! showAd(3) !!}

          </div>

        </div>

      </div>

    </div>

  </section>

  <!--====== End Equipments section ======-->



  <form class="d-none" action="{{ route('all_equipment') }}" method="GET">

    <input type="hidden" id="keyword-id" name="keyword"

      value="{{ !empty(request()->input('keyword')) ? request()->input('keyword') : '' }}">



    <input type="hidden" id="sort-id" name="sort"

      value="{{ !empty(request()->input('sort')) ? request()->input('sort') : '' }}">



    <input type="hidden" id="date-id" name="dates"

      value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">



    <input type="hidden" id="category-id" name="category"

      value="{{ !empty(request()->input('category')) ? request()->input('category') : '' }}">



    <input type="hidden" id="pricing-id" name="pricing"

      value="{{ !empty(request()->input('pricing')) ? request()->input('pricing') : '' }}">



    <input type="hidden" id="min-id" name="min"

      value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">



    <input type="hidden" id="max-id" name="max"

      value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">



    <button type="submit" id="submitBtn"></button>

  </form>

@endsection



@section('script')

  <script>

    'use strict';

    let currency_info = {!! json_encode($currencyInfo) !!};

    let position = currency_info.base_currency_symbol_position;

    let symbol = currency_info.base_currency_symbol;

    let min_price = {{ $minPrice }};

    let max_price = {{ $maxPrice }};

    let curr_min = {{ !empty(request()->input('min')) ? request()->input('min') : $minPrice }};

    let curr_max = {{ !empty(request()->input('max')) ? request()->input('max') : $maxPrice }};

  </script>



  <script type="text/javascript" src="{{ asset('assets/js/equipment.js') }}"></script>

@endsection

