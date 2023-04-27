@extends('frontend.layout')

@section('pageHeading')
  {{ __('Booking Details') }}
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => __('Booking Details'),
  ])

  <!--====== Start Dashboard Section ======-->
  <section class="user-dashboard pt-130 pb-120">
    <div class="container">
      <div class="row">
        @includeIf('frontend.user.side-navbar')

        <div class="col-lg-9">
          <div class="row">
            <div class="col-lg-12">
              <div class="user-profile-details">
                <div class="order-details">
                  <div class="title">
                    <h4>{{ __('Details') }}</h4>
                  </div>

                  <div class="view-order-page">
                    <div class="order-info-area">
                      <div class="row align-items-center">
                        <div class="col-lg-8">
                          <div class="order-info">
                            <h3>{{ __('Booking') . ': #' . $details->booking_number }}</h3>
                            <p>{{ __('Booking Date') . ': ' . date_format($details->created_at, 'M d, Y') }}</p>
                          </div>
                        </div>

                        @if (!is_null($details->invoice))
                          <div class="col-lg-4">
                            <div class="download">
                              <a href="{{ asset('assets/file/invoices/equipment/' . $details->invoice) }}" download
                                class="btn">{{ __('Invoice') }}</a>
                            </div>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>

                  <div class="billing-add-area">
                    <div class="row">
                      @php
                        $equipment = $details->equipmentInfo()->first();
                        $content = $equipment
                            ->content()
                            ->where('language_id', $language->id)
                            ->first();
                        $equipmentTitle = $content->title;
                        $equipmentSlug = $content->slug;
                        
                        $startDate = Carbon\Carbon::parse($details->start_date)->format('M d, Y');
                        $endDate = Carbon\Carbon::parse($details->end_date)->format('M d, Y');
                      @endphp

                      <div class="col-md-4">
                        <div class="main-info">
                          <h5>{{ __('Booking Information') }}</h5>
                          <ul class="list">
                            @if ($equipment->vendor)
                              <li>
                                <p><span>{{ __('Vendor') . ':' }}</span><a class="text-primary" target="_blank"
                                    href="{{ route('frontend.vendor.details', $equipment->vendor->username) }}">{{ $vendor = optional($equipment->vendor)->username }}</a>
                                </p>
                              </li>
                            @else
                              <li>
                                <p><span>{{ __('Vendor') . ':' }} {{ __('Admin') }}</span>
                                </p>
                              </li>
                            @endif
                            <li>
                              <p><span>{{ __('Equipment') . ':' }}</span><a class="text-primary" target="_blank"
                                  href="{{ route('equipment_details', ['slug' => $equipmentSlug]) }}">{{ $equipmentTitle }}</a>
                              </p>
                            </li>
                            <li>
                              <p><span>{{ __('Start Date') . ':' }}</span>{{ $startDate }}</p>
                            </li>
                            <li>
                              <p><span>{{ __('End Date') . ':' }}</span>{{ $endDate }}</p>
                            </li>

                            @if (!is_null($details->shipping_method))
                              <li>
                                <p><span>{{ __('Shipping Type') . ':' }}</span>{{ ucwords($details->shipping_method) }}
                                </p>
                              </li>
                            @endif

                            <li>
                              <p><span>{{ __('Location') . ':' }}</span>{{ $details->location }}</p>
                            </li>
                            <li>
                              <p>
                                <span>{{ __('Shipping Status') . ':' }}</span>
                                @if ($details->shipping_status == 'pending')
                                  <span class="badge badge-warning px-2 py-1">{{ __('Pending') }}</span>
                                @elseif ($details->shipping_status == 'delivered' || $details->shipping_status == 'taken')
                                  <span
                                    class="badge badge-primary px-2 py-1">{{ ucwords($details->shipping_status) }}</span>
                                @else
                                  <span class="badge badge-success px-2 py-1">{{ __('Returned') }}</span>
                                @endif
                              </p>
                            </li>
                          </ul>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="main-info">
                          <h5>{{ __('Billing Details') }}</h5>
                          <ul class="list">
                            <li>
                              <p><span>{{ __('Name') . ':' }}</span>{{ $details->name }}</p>
                            </li>
                            <li>
                              <p><span>{{ __('Email') . ':' }}</span>{{ $details->email }}</p>
                            </li>
                            <li>
                              <p><span>{{ __('Phone') . ':' }}</span>{{ $details->contact_number }}</p>
                            </li>
                          </ul>
                        </div>
                      </div>

                      @php
                        $position = $details->currency_symbol_position;
                        $symbol = $details->currency_symbol;
                        $subtotal = floatval($details->total) - floatval($details->discount);
                      @endphp

                      <div class="col-md-4">
                        <div class="main-info">
                          <h5>{{ __('Payment Information') }}</h5>
                          <ul class="list">
                            @if (!is_null($details->total))
                              <li>
                                <p>
                                  <span>{{ __('Price') . ':' }}</span>{{ $position == 'left' ? $symbol : '' }}{{ number_format($details->total, 2) }}{{ $position == 'right' ? $symbol : '' }}
                                </p>
                              </li>
                            @endif

                            @if (!is_null($details->discount))
                              <li>
                                <p><span class="text-success">{{ __('Discount') }} (<i
                                      class="far fa-minus"></i>):</span>{{ $position == 'left' ? $symbol : '' }}{{ $details->discount }}{{ $position == 'right' ? $symbol : '' }}
                                </p>
                              </li>
                            @endif

                            @if (!is_null($details->total) && !is_null($details->discount))
                              <li>
                                <p>
                                  <span>{{ __('Subtotal') . ':' }}</span>{{ $position == 'left' ? $symbol : '' }}{{ number_format($subtotal, 2) }}{{ $position == 'right' ? $symbol : '' }}
                                </p>
                              </li>
                            @endif

                            @if (!is_null($details->shipping_cost))
                              <li>
                                <p><span class="text-danger">{{ __('Shipping Cost') }} (<i
                                      class="far fa-plus"></i>):</span>{{ $position == 'left' ? $symbol : '' }}{{ $details->shipping_cost }}{{ $position == 'right' ? $symbol : '' }}
                                </p>
                              </li>
                            @endif

                            @if (!is_null($details->tax))
                              <li>
                                <p><span class="text-danger">{{ __('Tax') . ' (' . $tax->equipment_tax_amount . '%)' }}
                                    (<i
                                      class="far fa-plus"></i>):</span>{{ $position == 'left' ? $symbol : '' }}{{ $details->tax }}{{ $position == 'right' ? $symbol : '' }}
                                </p>
                              </li>
                            @endif

                            @if (!is_null($details->grand_total))
                              <li>
                                <p>
                                  <span>{{ __('Grand Total') . ':' }}</span>{{ $position == 'left' ? $symbol : '' }}{{ number_format($details->grand_total, 2) }}{{ $position == 'right' ? $symbol : '' }}
                                </p>
                              </li>
                            @endif

                            @if (is_null($details->payment_method))
                              <li>
                                <p><span>{{ __('Paid via') . ':' }}</span>{{ __('Negotiated') }}</p>
                              </li>
                            @else
                              <li>
                                <p><span>{{ __('Paid via') . ':' }}</span>{{ $details->payment_method }}</p>
                              </li>
                            @endif

                            <li>
                              <p>
                                <span>{{ __('Payment Status') . ':' }}</span>
                                @if ($details->payment_status == 'completed')
                                  <span class="badge badge-success px-2 py-1">{{ __('Completed') }}</span>
                                @elseif ($details->payment_status == 'pending')
                                  <span class="badge badge-warning px-2 py-1">{{ __('Pending') }}</span>
                                @else
                                  <span class="badge badge-danger px-2 py-1">{{ __('Rejected') }}</span>
                                @endif
                              </p>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--====== End Dashboard Section ======-->
@endsection
