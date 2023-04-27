@extends('vendors.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Booking Details') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('vendor.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Equipment Booking') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Bookings') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Booking Details') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    @php
      $position = $details->currency_symbol_position;
      $currency = $details->currency_symbol;
    @endphp

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Booking No.') . ' ' . '#' . $details->booking_number }}
          </div>
        </div>

        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Booking Date') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">{{ date_format($details->created_at, 'M d, Y') }}</div>
            </div>

            @if (is_null($details->total))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Price') . ' :' }}</strong>
                </div>

                <div class="col-lg-6">{{ __('Negotiable') }}</div>
              </div>
            @else
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Price') . ' :' }}</strong>
                </div>

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($details->total, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->discount))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Discount') }} <span class="text-success">(<i class="far fa-minus"></i>)</span>
                    :</strong>
                </div>

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($details->discount, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->total) && !is_null($details->discount))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Subtotal') . ' :' }}</strong>
                </div>

                @php
                  $total = floatval($details->total);
                  $discount = floatval($details->discount);
                  $subtotal = $total - $discount;
                @endphp

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($subtotal, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->shipping_cost))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Shipping Cost') }} <span class="text-danger">(<i class="far fa-plus"></i>)</span>
                    :</strong>
                </div>

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($details->shipping_cost, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->tax))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Tax') }} {{ '(' . $tax->equipment_tax_amount . '%)' }} <span
                      class="text-danger">(<i class="far fa-plus"></i>)</span> :</strong>
                </div>

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($details->tax, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->grand_total))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Grand Total') . ' :' }}</strong>
                </div>

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($details->grand_total, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->received_amount))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Received Amount') . ' :' }}</strong>
                </div>

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($details->received_amount, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->received_amount))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Commision') . ' :' }}</strong>
                </div>

                <div class="col-lg-6">
                  {{ $position == 'left' ? $currency . ' ' : '' }}{{ number_format($details->comission, 2) }}{{ $position == 'right' ? ' ' . $currency : '' }}
                </div>
              </div>
            @endif

            @if (!is_null($details->payment_method))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Paid via') . ' :' }}</strong>
                </div>

                <div class="col-lg-6">{{ $details->payment_method }}</div>
              </div>
            @endif

            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Payment Status') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                @if ($details->payment_status == 'completed')
                  <span class="badge badge-success">{{ __('Completed') }}</span>
                @elseif ($details->payment_status == 'pending')
                  <span class="badge badge-warning">{{ __('Pending') }}</span>
                @else
                  <span class="badge badge-danger">{{ __('Rejected') }}</span>
                @endif
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-lg-6">
                <strong>{{ __('Shipping Status') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                @if ($details->shipping_status == 'pending')
                  <span class="badge badge-warning">{{ __('Pending') }}</span>
                @elseif ($details->shipping_status == 'taken')
                  <span class="badge badge-primary">{{ __('Taken') }}</span>
                @elseif ($details->shipping_status == 'delivered')
                  <span class="badge badge-primary">{{ __('Delivered') }}</span>
                @else
                  <span class="badge badge-success">{{ __('Returned') }}</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Booking Information') }}
          </div>
        </div>

        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Equipment') . ' :' }}</strong>
              </div>

              @php
                $equipment = $details->equipmentInfo()->first();
                $equipmentTitle = $equipment
                    ->content()
                    ->where('language_id', $language->id)
                    ->select('title', 'slug')
                    ->first();
              @endphp

              <div class="col-lg-8"><a target="_blank"
                  href="{{ route('equipment_details', $equipmentTitle->slug) }}">{{ strlen($equipmentTitle->title) > 20 ? mb_substr($equipmentTitle->title, 0, 20, 'UTF-8') . '...' : $equipmentTitle->title }}</a>
              </div>
            </div>

            @php
              $startDate = Carbon\Carbon::parse($details->start_date)->format('M d, Y');
              $endDate = Carbon\Carbon::parse($details->end_date)->format('M d, Y');
            @endphp

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Start Date') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">{{ $startDate }}</div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('End Date') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">{{ $endDate }}</div>
            </div>

            @if (!is_null($details->shipping_method))
              <div class="row mb-2">
                <div class="col-lg-4">
                  <strong>{{ __('Shipping Type') . ' :' }}</strong>
                </div>

                <div class="col-lg-8">{{ ucwords($details->shipping_method) }}</div>
              </div>
            @endif

            <div class="row mb-1">
              <div class="col-lg-4">
                <strong>{{ __('Shipping Location') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">{{ $details->location }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Billing Details') }}
          </div>
        </div>

        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Name') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">{{ $details->name }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Username') . ' :' }}</strong>
              </div>
              @php
                $user = $details->user()->first();
              @endphp
              @if ($user)
                <div class="col-lg-8">{{ $user->username }}</div>
              @else
                <div class="col-lg-8">{{ __('Guest') }}</div>
              @endif
            </div>

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Email') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">{{ $details->email }}</div>
            </div>

            <div class="row mb-1">
              <div class="col-lg-4">
                <strong>{{ __('Contact Number') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">{{ $details->contact_number }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
