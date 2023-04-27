@extends('frontend.layout')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->equipment_details_page_title }}
  @endif
@endsection

@section('metaKeywords')
  {{ $details->meta_keywords }}
@endsection

@section('metaDescription')
  {{ $details->meta_description }}
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => Str::limit($details->title, 20, '...'),
  ])

  <!--====== Start Equipment Details Section ======-->
  <section class="equipment-details-section pt-130 pb-110">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          @php $sliderImages = json_decode($details->slider_images); @endphp

          <div class="equipment-gallery-box d-flex mb-40">
            <div class="equipment-slider-wrap">
              <div class="equipment-gallery-slider">
                @foreach ($sliderImages as $sliderImage)
                  <div class="single-gallery-item"
                    data-thumb="{{ asset('assets/img/equipments/slider-images/' . $sliderImage) }}">
                    <a href="{{ asset('assets/img/equipments/slider-images/' . $sliderImage) }}" class="img-popup">
                      <img data-src="{{ asset('assets/img/equipments/slider-images/' . $sliderImage) }}" alt="image"
                        class="lazy">
                    </a>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="equipment-gallery-arrow"></div>
          </div>

          <div class="description-wrapper">
            <h3 class="title mb-2">{{ $details->title }}</h3>
            <h6>{{ optional($details->vendor)->shop_name }}</h6>
            <div class="vendor-name">
              @if ($details->vendor)
                {{ __('By') }}
                <a href="{{ route('frontend.vendor.details', $details->vendor->username) }}">
                  {{ $vendor = optional($details->vendor)->username }}
                </a>
              @else
                {{ __('By') }} {{ __('Admin') }}</a>
              @endif
            </div>

            <br>
            <a href="#" class="voucher-btn category-search" data-category_slug="{{ $details->categorySlug }}">
              {{ $details->categoryName }}
            </a>

            <div class="description-tabs">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#description">{{ __('Description') }}</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#features">{{ __('Features') }}</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#reviews">{{ __('Reviews') }}</a>
                </li>
              </ul>
            </div>

            <div class="tab-content mt-30">
              <div id="description" class="tab-pane fade show active">
                <div class="description-content-box">
                  <p>{!! replaceBaseUrl($details->description, 'summernote') !!}</p>
                </div>
              </div>

              <div id="features" class="tab-pane fade">
                <div class="features-content-box">
                  @php $features = explode(PHP_EOL, $details->features); @endphp

                  <div class="content-table table-responsive">
                    <table class="table">
                      <tbody>
                        @foreach ($features as $feature)
                          <tr>
                            <td>{{ $feature }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div id="reviews" class="tab-pane fade">
                <div class="equipment-review-content-box">
                  @if (count($reviews) == 0)
                    <h5 class="mb-30">{{ __('This equipment has no review yet') . '!' }}</h5>
                  @else
                    @foreach ($reviews as $review)
                      <div class="equipment-review-user d-flex">
                        <div class="thumb">
                          @if (empty($review->user->image))
                            <img data-src="{{ asset('assets/img/user.png') }}" alt="image" class="lazy">
                          @else
                            <img data-src="{{ asset('assets/img/users/' . $review->user->image) }}" alt="image"
                              class="lazy">
                          @endif
                        </div>

                        <div class="content">
                          <ul class="rating lh-1">
                            @for ($i = 0; $i < $review->rating; $i++)
                              <li><i class="fas fa-star"></i></li>
                            @endfor
                          </ul>

                          @php
                            $name = $review->user->username;
                            $date = date_format($review->created_at, 'F d, Y');
                          @endphp

                          <span
                            class="date"><span>{{ $name == ' ' ? 'User' : $name }}</span>{{ ' – ' . $date }}</span>
                          <p>{{ $review->comment }}</p>
                        </div>
                      </div>
                    @endforeach
                  @endif

                  @guest('web')
                    <a href="{{ route('user.login', ['redirect_path' => 'equipment-details']) }}" class="main-btn">
                      {{ __('Login') }}
                    </a>
                  @endguest

                  @auth('web')
                    <div class="equipment-review-form">
                      <form action="{{ route('equipment_details.store_review', ['id' => $details->id]) }}" method="POST">
                        @csrf
                        <div class="form_group">
                          <label>{{ __('Comment') }}</label>
                          <textarea class="form_control" name="comment">{{ old('comment') }}</textarea>
                        </div>

                        <div class="form_group">
                          <label>{{ __('Rating') . '*' }}</label>
                          <ul class="rating mb-20">
                            <li class="review-value review-1">
                              <span class="fas fa-star" data-ratingVal="1"></span>
                            </li>

                            <li class="review-value review-2">
                              <span class="fas fa-star" data-ratingVal="2"></span>
                              <span class="fas fa-star" data-ratingVal="2"></span>
                            </li>

                            <li class="review-value review-3">
                              <span class="fas fa-star" data-ratingVal="3"></span>
                              <span class="fas fa-star" data-ratingVal="3"></span>
                              <span class="fas fa-star" data-ratingVal="3"></span>
                            </li>

                            <li class="review-value review-4">
                              <span class="fas fa-star" data-ratingVal="4"></span>
                              <span class="fas fa-star" data-ratingVal="4"></span>
                              <span class="fas fa-star" data-ratingVal="4"></span>
                              <span class="fas fa-star" data-ratingVal="4"></span>
                            </li>

                            <li class="review-value review-5">
                              <span class="fas fa-star" data-ratingVal="5"></span>
                              <span class="fas fa-star" data-ratingVal="5"></span>
                              <span class="fas fa-star" data-ratingVal="5"></span>
                              <span class="fas fa-star" data-ratingVal="5"></span>
                              <span class="fas fa-star" data-ratingVal="5"></span>
                            </li>
                          </ul>
                        </div>

                        <input type="hidden" id="rating-id" name="rating">

                        <div class="form_group">
                          <button type="submit" class="main-btn">
                            {{ __('Submit') }}
                          </button>
                        </div>
                      </form>
                    </div>
                  @endauth
                </div>
              </div>
            </div>
          </div>

          <div class="text-center mt-70">
            {!! showAd(3) !!}
          </div>
        </div>

        <div class="col-lg-4">
          <div class="equipement-sidebar-info">
            <form action="{{ route('equipment.make_booking') }}" method="POST" enctype="multipart/form-data"
              id="equipment-booking-form">
              @csrf
              <input type="hidden" name="equipment_id" value="{{ $details->id }}">

              <div class="booking-form">
                @php
                  $position = $currencyInfo->base_currency_symbol_position;
                  $symbol = $currencyInfo->base_currency_symbol;
                  
                  // calculate tax
                  $currTotal = $details->lowest_price;
                  $taxAmount = $basicData['equipment_tax_amount'];
                  $calculatedTax = $currTotal * ($taxAmount / 100);
                  
                  // calculate grand total
                  $grandTotal = $currTotal + $calculatedTax;
                @endphp

                <div class="price-info">
                  <h5>{{ __('Price') }}</h5>

                  <div class="price-tag">
                    @if (!empty($currTotal))
                      <h4 dir="ltr">{{ $position == 'left' ? $symbol : '' }}<span
                          id="booking-price">{{ number_format($currTotal, 2) }}</span>{{ $position == 'right' ? $symbol : '' }}
                      </h4>
                    @endif
                  </div>
                </div>

                <div class="pricing-body">
                  {{-- show error message for request-price-message --}}
                  @error('price_message')
                    <div class="row">
                      <div class="col">
                        <div class="alert alert-danger alert-block">
                          <strong>{{ $message }}</strong>
                          <button type="button" class="close" data-dismiss="alert">×</button>
                        </div>
                      </div>
                    </div>
                  @enderror

                  <div class="price-option">
                    @if (!empty($details->per_day_price))
                      <span
                        class="span-btn day">{{ $position == 'left' ? $symbol : '' }}{{ $details->per_day_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Day') }}</span>
                    @endif

                    @if (!empty($details->per_week_price))
                      <span
                        class="span-btn week">{{ $position == 'left' ? $symbol : '' }}{{ $details->per_week_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Week') }}</span>
                    @endif

                    @if (!empty($details->per_month_price))
                      <span
                        class="span-btn month">{{ $position == 'left' ? $symbol : '' }}{{ $details->per_month_price }}{{ $position == 'right' ? $symbol : '' }}{{ '/' . __('Month') }}</span>
                    @endif
                  </div>

                  @if (Auth::guard('web')->check() == false && $basicData['guest_checkout_status'] == 1)
                    <div class="alert alert-warning mb-0 mt-4">
                      {{ __('You are now booking as a guest') . '. ' . __('if you want to log in before booking') . ', ' . __('then please') }}
                      <a href="{{ route('user.login', ['redirect_path' => 'equipment-details']) }}"
                        id="login-link">{{ __('Click Here') }}</a>
                    </div>
                  @endif

                  <div class="form_group">
                    <div class="input-wrap">
                      <input type="text" id="date-range" placeholder="{{ __('Select Booking Date') }}"
                        name="dates" value="{{ old('dates') }}" readonly>
                      <i class="far fa-calendar-alt"></i>

                      <p id="booking-day" class="mt-2 {{ $currentLanguageInfo->direction == 1 ? 'mr-3' : 'ml-3' }}">
                      </p>

                      @error('dates')
                        <p class="text-danger mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="input-wrap mt-3">
                      @guest('web')
                        <input type="text" placeholder="{{ __('Enter Full Name') }}" name="name"
                          value="{{ old('name') }}">
                      @endguest

                      @auth('web')
                        @php
                          $name = Auth::guard('web')->user()->first_name;
                          if (!empty(Auth::guard('web')->user()->last_name)) {
                              $name = $name . ' ' . Auth::guard('web')->user()->last_name;
                          }
                        @endphp
                        <input type="text" placeholder="{{ __('Enter Full Name') }}" name="name"
                          value="{{ $name }}">
                      @endauth
                      <i class="far fa-user"></i>

                      @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="input-wrap mt-3">
                      @guest('web')
                        <input type="text" placeholder="{{ __('Enter Contact Number') }}" name="contact_number"
                          value="{{ old('contact_number') }}">
                      @endguest

                      @auth('web')
                        <input type="text" placeholder="{{ __('Enter Contact Number') }}" name="contact_number"
                          value="{{ Auth::guard('web')->user()->contact_number }}">
                      @endauth
                      <i class="far fa-phone"></i>

                      @error('contact_number')
                        <p class="text-danger mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="input-wrap mt-3">
                      @guest('web')
                        <input type="email" placeholder="{{ __('Enter Email') }}" name="email"
                          value="{{ old('email') }}">
                      @endguest

                      @auth('web')
                        <input type="email" placeholder="{{ __('Enter Email') }}" name="email"
                          value="{{ Auth::guard('web')->user()->email }}">
                      @endauth
                      <i class="far fa-envelope"></i>

                      @error('email')
                        <p class="text-danger mt-1">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>

                  @php $shippingMethod = session()->get('shippingMethod'); @endphp

                  <div class="form_group">
                    @if ($basicData['self_pickup_status'] == 1 || $basicData['two_way_delivery_status'] == 1)
                      <div class="reserved-filter d-flex justify-content-between">
                        @if ($basicData['self_pickup_status'] == 1)
                          <div class="single-method d-flex">
                            <input type="radio" id="self-pickup" name="shipping_method" value="self pickup"
                              {{ $shippingMethod == 'self pickup' ? 'checked' : '' }}>
                            <label for="self-pickup"><span>{{ __('Self Pickup') }}</span></label>
                          </div>
                        @endif

                        @if ($basicData['two_way_delivery_status'] == 1)
                          <div class="single-method d-flex">
                            <input type="radio" id="two-way-delivery" name="shipping_method"
                              value="two way delivery" {{ $shippingMethod == 'two way delivery' ? 'checked' : '' }}>
                            <label for="two-way-delivery"><span>{{ __('Two Way Delivery') }}</span></label>
                          </div>
                        @endif
                      </div>

                      @error('shipping_method')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    @endif

                    <div id="reload-div">
                      <div id="location-wrapper">
                        @if ($shippingMethod == 'self pickup')
                          <div id="self-pickup-select" class="mt-4">
                            @if (count($locations) > 0)
                              <select name="location" class="wide form_control">
                                <option selected disabled>{{ __('Select a Location') }}</option>

                                @foreach ($locations as $location)
                                  <option value="{{ $location->id }}"
                                    {{ $location->id == old('location') ? 'selected' : '' }}>
                                    {{ $location->name }}
                                  </option>
                                @endforeach
                              </select>
                            @endif
                            @error('location')
                              <p class="text-danger mt-2">{{ $message }}</p>
                            @enderror
                          </div>
                        @endif

                        @if ($shippingMethod == 'two way delivery')
                          <div id="two-way-delivery-select" class="mt-4">
                            @if (count($locations) > 0)
                              <select name="location" class="wide form_control">
                                <option selected disabled>{{ __('Select a Location') }}</option>

                                @foreach ($locations as $location)
                                  <option value="{{ $location->id }}" data-charge="{{ $location->charge }}"
                                    {{ $location->id == old('location') ? 'selected' : '' }}>
                                    {{ $location->name }} @if ($basicData['two_way_delivery_status'] == 1)
                                      (+
                                      {{ $position == 'left' ? $symbol : '' }}{{ $location->charge }}{{ $position == 'right' ? $symbol : '' }})
                                    @endif
                                  </option>
                                @endforeach
                              </select>

                              @if ($basicData['two_way_delivery_status'] == 1)
                                <p class="mt-2 text-info">
                                  {{ __('Shipping charge is only applicable for') . ' "' . __('two way delivery') . '".' }}
                                </p>
                              @endif
                            @endif
                            @error('location')
                              <p class="text-danger mt-2">{{ $message }}</p>
                            @enderror
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>

                  <div class="extra-option pt-35 pb-35">
                    <div class="form-group d-flex">
                      <input type="text" class="form-control" id="coupon-code"
                        placeholder="{{ __('Enter Your Coupon') }}">
                      <button class="btn" onclick="applyCoupon(event)">{{ __('Apply') }}</button>
                    </div>

                    <div class="price-option-table mt-4">
                      <ul>
                        <li class="single-price-option">
                          <span class="title">{{ __('Discount') }} <span class="text-success">(<i
                                class="fas fa-minus"></i>)</span> <span class="amount"
                              dir="ltr">{{ $position == 'left' ? $symbol : '' }}<span id="discount-amount"
                                dir="ltr">0.00</span>{{ $position == 'right' ? $symbol : '' }}</span></span>
                        </li>

                        <li class="single-price-option">
                          <span class="title">{{ __('Subtotal') }} <span class="amount"
                              dir="ltr">{{ $position == 'left' ? $symbol : '' }}<span id="subtotal-amount"
                                dir="ltr">{{ number_format($currTotal, 2) }}</span>{{ $position == 'right' ? $symbol : '' }}</span></span>
                        </li>

                        <li class="single-price-option">
                          <span class="title">{{ __('Tax') }}
                            <span dir="ltr">{{ '(' . $basicData['equipment_tax_amount'] . '%)' }}</span>
                            <span class="text-danger">(<i class="fas fa-plus"></i>)</span> <span class="amount"
                              dir="ltr">{{ $position == 'left' ? $symbol : '' }}<span id="tax-amount"
                                dir="ltr">{{ number_format($calculatedTax, 2) }}</span>{{ $position == 'right' ? $symbol : '' }}</span></span>
                        </li>

                        @if ($basicData['two_way_delivery_status'] == 1)
                          <li class="single-price-option">
                            <span class="title">{{ __('Shipping Charge') }} <span class="text-danger">(<i
                                  class="fas fa-plus"></i>)</span> <span class="amount"
                                dir="ltr">{{ $position == 'left' ? $symbol : '' }}<span id="shipping-charge"
                                  dir="ltr">0.00</span>{{ $position == 'right' ? $symbol : '' }}</span></span>
                          </li>
                        @endif

                        <li class="single-price-option">
                          <span class="title">{{ __('Grand Total') }} <span class="amount"
                              dir="ltr">{{ $position == 'left' ? $symbol : '' }}<span id="grand-total"
                                dir="ltr">{{ number_format($grandTotal, 2) }}</span>{{ $position == 'right' ? $symbol : '' }}</span></span>
                        </li>
                      </ul>
                    </div>
                  </div>

                  @if ($details->price_btn_status == 0 && (count($onlineGateways) > 0 || count($offlineGateways) > 0))
                    <div class="form_group">
                      <select name="gateway" class="form_control">
                        <option selected disabled>{{ __('Select Payment Gateway') }}</option>

                        @if (count($onlineGateways) > 0)
                          @foreach ($onlineGateways as $onlineGateway)
                            <option value="{{ $onlineGateway->keyword }}"
                              {{ $onlineGateway->keyword == old('gateway') ? 'selected' : '' }}>
                              {{ __($onlineGateway->name) }}
                            </option>
                          @endforeach
                        @endif

                        @if (count($offlineGateways) > 0)
                          @foreach ($offlineGateways as $offlineGateway)
                            <option value="{{ $offlineGateway->id }}"
                              {{ $offlineGateway->id == old('gateway') ? 'selected' : '' }}>
                              {{ __($offlineGateway->name) }}
                            </option>
                          @endforeach
                        @endif
                      </select>

                      @php
                        $stripeExist = false;
                        
                        if (count($onlineGateways) > 0) {
                            foreach ($onlineGateways as $onlineGateway) {
                                if ($onlineGateway->keyword == 'stripe') {
                                    $stripeExist = true;
                                    break;
                                }
                            }
                        }
                      @endphp

                      @if ($stripeExist == true)
                        <div id="stripe-card-input"
                          class="mt-4 @if ($errors->has('card_number') ||
                              $errors->has('cvc_number') ||
                              $errors->has('expiry_month') ||
                              $errors->has('expiry_year')) d-block @else d-none @endif">
                          <div class="input-wrap">
                            <input type="text" name="card_number" placeholder="{{ __('Enter Your Card Number') }}"
                              autocomplete="off" oninput="checkCard(this.value)">
                            <p class="mt-1 text-danger" id="card-error"></p>

                            @error('card_number')
                              <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                          </div>

                          <div class="input-wrap mt-3">
                            <input type="text" name="cvc_number" placeholder="{{ __('Enter CVC Number') }}"
                              autocomplete="off" oninput="checkCVC(this.value)">
                            <p class="mt-1 text-danger" id="cvc-error"></p>

                            @error('cvc_number')
                              <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                          </div>

                          <div class="input-wrap mt-3">
                            <input type="text" name="expiry_month" placeholder="{{ __('Enter Expiry Month') }}">

                            @error('expiry_month')
                              <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                          </div>

                          <div class="input-wrap mt-3">
                            <input type="text" name="expiry_year" placeholder="{{ __('Enter Expiry Year') }}">

                            @error('expiry_year')
                              <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                          </div>
                        </div>
                      @endif

                      @foreach ($offlineGateways as $offlineGateway)
                        <div id="{{ 'offline-gateway-' . $offlineGateway->id }}"
                          class="offline-gateway-info @if ($errors->has('attachment') &&
                              request()->session()->get('gatewayId') == $offlineGateway->id) d-block @else d-none @endif">
                          @if ($offlineGateway->has_attachment == 1)
                            <div class="input-wrap mt-3">
                              <label>{{ __('Attachment') . '*' }}</label>
                              <br>
                              <input type="file" name="attachment" id="offline-gateway-attachment">

                              @error('attachment')
                                <p class="text-danger mt-1">{{ $message }}</p>
                              @enderror
                            </div>
                          @endif

                          @if (!is_null($offlineGateway->short_description))
                            <div class="input-wrap mt-3">
                              <label>{{ __('Description') }}</label>
                              <p>{{ $offlineGateway->short_description }}</p>
                            </div>
                          @endif

                          @if (!is_null($offlineGateway->instructions))
                            <div class="input-wrap mt-3">
                              <label>{{ __('Instructions') }}</label>
                              <p>{!! replaceBaseUrl($offlineGateway->instructions, 'summernote') !!}</p>
                            </div>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  @endif
                  @if ($basicData['guest_checkout_status'] == 1)
                    <div class="button text-center mt-30">
                      <button type="submit" class="main-btn">{{ __('Book Now') }}</button>
                    </div>
                  @elseif(Auth::guard('web')->check() == false && $basicData['guest_checkout_status'] == 0)
                    <div class="button text-center mt-30">
                      <a href="{{ route('user.login', ['redirect_path' => 'equipment-details']) }}" class="main-btn">
                        {{ __('Login') }}
                      </a>
                    </div>
                  @else
                    <div class="button text-center mt-30">
                      <button type="submit" class="main-btn">{{ __('Book Now') }}</button>
                    </div>
                  @endif
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--====== End Equipment Details Section ======-->

  <!-- Request Price Modal -->
  <div class="modal fade" id="requestPriceModal" tabindex="-1" role="dialog"
    aria-labelledby="requestPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="requestPriceModalLabel">{{ __('Request Equipment Price') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <textarea class="form-control mt-3" id="message-text" rows="7"
              placeholder="{{ __('Write Your Message Here') }}"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-warning" id="modal-submit-btn">{{ __('Submit') }}</button>
        </div>
      </div>
    </div>
  </div>

  {{-- equipment search form start --}}
  <form class="d-none" action="{{ route('all_equipment') }}" method="GET">
    <input type="hidden" id="category-id" name="category">

    <button type="submit" id="submitBtn"></button>
  </form>
  {{-- equipment search form end --}}
@endsection

@section('script')
  <script>
    'use strict';
    let minBookingDays = {{ $details->min_booking_days }};
    let maxBookingDays = {{ $details->max_booking_days }};
    let equipmentId = {{ $details->id }};
    const tax = {{ $basicData['equipment_tax_amount'] }};
    const twoWayDeliveryStatus = {{ $basicData['two_way_delivery_status'] }};
    let dateArray = {!! json_encode($bookedDates) !!};
    const numDayStr = "{{ __('Number of Days') }}";
    const maxDayStr = "{{ __('Maximum booking day is') }}";
    const minDayStr = "{{ __('Minimum booking day is') }}";
  </script>

  <script src="{{ asset('assets/js/stripe.js') }}"></script>

  <script type="text/javascript" src="{{ asset('assets/js/equipment.js') }}"></script>
@endsection
