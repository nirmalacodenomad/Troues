<!DOCTYPE html>
<html>

<head lang="{{ $currentLanguageInfo->code }}" @if ($currentLanguageInfo->direction == 1) dir="rtl" @endif>
  {{-- required meta tags --}}
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  {{-- title --}}
  <title>{{ 'Equipment Invoice | ' . config('app.name') }}</title>

  {{-- fav icon --}}
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/' . $websiteInfo->favicon) }}">

  {{-- styles --}}
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
</head>

<body>
  @php
    $mb = '35px';
    $width = '50%';
    $ml = '18px';
    $floatR = 'right';
    $floatL = 'left';
    
  @endphp
  <div class="equipment-booking-invoice my-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="logo text-center ml-auto mr-auto" style="margin-bottom: {{ $mb }}; max-width: 180px;">
            <img class="img-fluid" src="{{ public_path('assets/img/' . $websiteInfo->logo) }}" alt="website logo">
          </div>

          <div class="bg-primary">
            <h2 class="text-center text-light pt-2">
              {{ __('EQUIPMENT BOOKING INVOICE') }}
            </h2>
          </div>
        </div>
      </div>

      @php
        $position = $bookingInfo->currency_text_position;
        $currency = $bookingInfo->currency_text;
      @endphp

      {{-- booking details start --}}
      <div style="width: {{ $width }}; float: {{ $floatL }};">
        <div class="mt-4 mb-1">
          <h4><strong>{{ __('Booking Details') }}</strong></h4>
        </div>

        <p>
          <strong>{{ __('Booking No') . ': ' }}</strong>{{ '#' . $bookingInfo->booking_number }}
        </p>
        <p>
          <strong>{{ __('Vendor') . ': ' }}</strong>

          @if ($bookingInfo->vendor)
            <a href="{{ route('frontend.vendor.details', $bookingInfo->vendor->username) }}"
              target="_blank">{{ $vendor = optional($bookingInfo->vendor)->username }}</a>
          @endif
        </p>
        <p>
          <strong>{{ __('Customer') . ': ' }}</strong>

          @if ($bookingInfo->user)
            <span class="badge badge-success">{{ $vendor = optional($bookingInfo->user)->username }}</span>
          @endif
        </p>

        <p>
          <strong>{{ __('Booking Date') . ': ' }}</strong>{{ $bookingInfo->created_at->toFormattedDateString() }}
        </p>

        @php
          $startDate = Carbon\Carbon::parse($bookingInfo->start_date);
          $endDate = Carbon\Carbon::parse($bookingInfo->end_date);
        @endphp

        <p>
          <strong>{{ __('Start Date') . ': ' }}</strong>{{ $startDate->toFormattedDateString() }}
        </p>

        <p>
          <strong>{{ __('End Date') . ': ' }}</strong>{{ $endDate->toFormattedDateString() }}
        </p>

        @if (is_null($bookingInfo->total))
          <p><strong>{{ __('Price') . ': ' }}</strong>{{ __('Negotiable') }}</p>
        @else
          <p>
            <strong>{{ __('Price') . ': ' }}</strong>{{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($bookingInfo->total, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
          </p>
        @endif

        @if (!is_null($bookingInfo->discount))
          <p>
            <strong>{{ __('Discount') . ': ' }}</strong>{{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($bookingInfo->discount, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
          </p>
        @endif

        @if (!is_null($bookingInfo->total) && !is_null($bookingInfo->discount))
          <p>
            @php
              $total = floatval($bookingInfo->total);
              $discount = floatval($bookingInfo->discount);
              $subtotal = $total - $discount;
            @endphp

            <strong>{{ __('Subtotal') . ': ' }}</strong>{{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($subtotal, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
          </p>
        @endif

        @if (!is_null($bookingInfo->shipping_cost))
          <p>
            <strong>{{ __('Shipping Cost') . ': ' }}</strong>{{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($bookingInfo->shipping_cost, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
          </p>
        @endif

        @if (!is_null($bookingInfo->grand_total))
          <p>
            <strong>{{ __('Grand Total') . ': ' }}</strong>{{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($bookingInfo->grand_total, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
          </p>
        @endif

        @if (!is_null($bookingInfo->payment_method))
          <p>
            <strong>{{ __('Payment Method') . ': ' }}</strong>{{ $bookingInfo->payment_method }}
          </p>
        @endif

        <p>
          <strong>{{ __('Payment Status') . ': ' }}</strong>{{ ucfirst($bookingInfo->payment_status) }}
        </p>

        <p>
          <strong>{{ __('Shipping Location') . ': ' }}</strong>{{ $bookingInfo->location }}
        </p>
        <p>
          <strong>{{ __('Shipping Method') . ': ' }}</strong>{{ $bookingInfo->shipping_method }}
        </p>
      </div>
      {{-- booking details end --}}

      {{-- billing details start --}}
      <div style="width: {{ $width }}; float: {{ $floatR }};">
        <div class="mt-4 mb-1">
          <h4><strong>{{ __('Billing Details') }}</strong></h4>
        </div>

        <p>
          <strong>{{ __('Name') . ': ' }}</strong>{{ $bookingInfo->name }}
        </p>

        <p>
          <strong>{{ __('Email') . ': ' }}</strong>{{ $bookingInfo->email }}
        </p>

        <p>
          <strong>{{ __('Contact Number') . ': ' }}</strong>{{ $bookingInfo->contact_number }}
        </p>
      </div>
      {{-- billing details end --}}
    </div>
  </div>
</body>

</html>
