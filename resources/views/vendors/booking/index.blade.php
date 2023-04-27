@extends('vendors.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Bookings') }}</h4>
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
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-10">
              <form id="searchForm" action="{{ route('vendor.equipment_booking.bookings') }}" method="GET">
                <input type="hidden" name="language" value="{{ $defaultLang->code }}">

                <div class="row">
                  <div class="col-lg-2">
                    <div class="form-group">
                      <label>{{ __('Booking Number') }}</label>
                      <input name="booking_no" type="text" class="form-control"
                        placeholder="{{ __('Search Here...') }}"
                        value="{{ !empty(request()->input('booking_no')) ? request()->input('booking_no') : '' }}">
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <div class="form-group">
                      <label>{{ __('Payment') }}</label>
                      <select class="form-control h-42" name="payment_status"
                        onchange="document.getElementById('searchForm').submit()">
                        <option value="" {{ empty(request()->input('payment_status')) ? 'selected' : '' }}>
                          {{ __('All') }}
                        </option>
                        <option value="completed"
                          {{ request()->input('payment_status') == 'completed' ? 'selected' : '' }}>
                          {{ __('Completed') }}
                        </option>
                        <option value="pending" {{ request()->input('payment_status') == 'pending' ? 'selected' : '' }}>
                          {{ __('Pending') }}
                        </option>
                        <option value="rejected"
                          {{ request()->input('payment_status') == 'rejected' ? 'selected' : '' }}>
                          {{ __('Rejected') }}
                        </option>
                      </select>
                    </div>
                  </div>

                  @if ($basicData->self_pickup_status == 1 && $basicData->two_way_delivery_status == 1)
                    <div class="col-lg-2">
                      <div class="form-group">
                        <label>{{ __('Shipping Type') }}</label>
                        <select class="form-control h-42" name="shipping_type"
                          onchange="document.getElementById('searchForm').submit()">
                          <option value="" {{ empty(request()->input('shipping_type')) ? 'selected' : '' }}>
                            {{ __('All') }}
                          </option>
                          <option value="self pickup"
                            {{ request()->input('shipping_type') == 'self pickup' ? 'selected' : '' }}>
                            {{ __('Self Pickup') }}
                          </option>
                          <option value="two way delivery"
                            {{ request()->input('shipping_type') == 'two way delivery' ? 'selected' : '' }}>
                            {{ __('Two Way Delivery') }}
                          </option>
                        </select>
                      </div>
                    </div>
                  @endif

                  <div class="col-lg-2">
                    <div class="form-group">
                      <label>{{ __('Shipping') }}</label>
                      <select class="form-control h-42" name="shipping_status"
                        onchange="document.getElementById('searchForm').submit()">
                        <option value="" {{ empty(request()->input('shipping_status')) ? 'selected' : '' }}>
                          {{ __('All') }}
                        </option>
                        <option value="pending" {{ request()->input('shipping_status') == 'pending' ? 'selected' : '' }}>
                          {{ __('Pending') }}
                        </option>
                        <option value="taken" {{ request()->input('shipping_status') == 'taken' ? 'selected' : '' }}>
                          {{ __('Taken') }}
                        </option>
                        <option value="delivered"
                          {{ request()->input('shipping_status') == 'delivered' ? 'selected' : '' }}>
                          {{ __('Delivered') }}
                        </option>
                        <option value="returned"
                          {{ request()->input('shipping_status') == 'returned' ? 'selected' : '' }}>
                          {{ __('Returned') }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <div class="col-lg-2">
              <button class="btn btn-danger btn-sm d-none bulk-delete float-lg-right"
                data-href="{{ route('vendor.equipment_booking.bulk_delete') }}" class="card-header-button">
                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($bookings) == 0)
                <h3 class="text-center mt-3">{{ __('NO BOOKING FOUND') . '!' }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-2">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{ __('Booking No.') }}</th>
                        <th scope="col">{{ __('Title') }}</th>
                        <th scope="col">{{ __('Customer') }}</th>
                        <th scope="col">{{ __('Total') }}</th>
                        <th scope="col">{{ __('Received Amount') }}</th>
                        <th scope="col">{{ __('Payment Status') }}</th>

                        @if ($basicData->self_pickup_status == 1 || $basicData->two_way_delivery_status == 1)
                          <th scope="col">{{ __('Shipping Type') }}</th>
                        @endif

                        <th scope="col">{{ __('Shipping Status') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($bookings as $booking)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $booking->id }}">
                          </td>
                          <td>{{ '#' . $booking->booking_number }}</td>
                          <td>
                            <a target="_blank"
                              href="{{ route('equipment_details', $booking->equipmentTitle->slug) }}">{{ strlen($booking->equipmentTitle->title) > 20 ? mb_substr($booking->equipmentTitle->title, 0, 20, 'UTF-8') . '...' : $booking->equipmentTitle->title }}</a>
                          </td>
                          <td>
                            @php
                              $user = $booking->user()->first();
                            @endphp
                            @if ($user)
                              {{ $user->username }}
                            @else
                              {{ __('Guest') }}
                            @endif
                          </td>
                          <td>
                            @if (is_null($booking->booking_type))
                              {{ $booking->currency_symbol_position == 'left' ? $booking->currency_symbol : '' }}{{ $booking->grand_total }}{{ $booking->currency_symbol_position == 'right' ? $booking->currency_symbol : '' }}
                            @else
                              <a href="#" class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#priceMsgModal-{{ $booking->id }}">
                                {{ __('Requested') }}
                              </a>
                            @endif
                          </td>
                          <td>
                            {{ $booking->currency_symbol_position == 'left' ? $booking->currency_symbol : '' }}{{ $booking->received_amount }}{{ $booking->currency_symbol_position == 'right' ? $booking->currency_symbol : '' }}
                          </td>
                          <td>
                            @if ($booking->gateway_type == 'online')
                              <h2 class="d-inline-block"><span class="badge badge-success">{{ __('Completed') }}</span>
                              </h2>
                            @else
                              @if ($booking->payment_status == 'completed')
                                <h2 class="d-inline-block"><span class="badge badge-success">{{ __('Completed') }}</span>
                                @elseif($booking->payment_status == 'pending')
                                  <h2 class="d-inline-block"><span class="badge badge-warning">{{ __('Pending') }}</span>
                                  @elseif($booking->payment_status == 'rejected')
                                    <h2 class="d-inline-block"><span
                                        class="badge badge-danger">{{ __('Rejected') }}</span>
                              @endif
                            @endif
                          </td>

                          @if ($basicData->self_pickup_status == 1 || $basicData->two_way_delivery_status == 1)
                            <td>{{ ucwords($booking->shipping_method) }}</td>
                          @endif

                          <td>
                            <form id="shippingStatusForm-{{ $booking->id }}" class="d-inline-block"
                              action="{{ route('vendor.equipment_booking.update_shipping_status', ['id' => $booking->id]) }}"
                              method="post">
                              @csrf
                              <select
                                class="form-control form-control-sm @if ($booking->shipping_status == 'pending') bg-warning text-dark @elseif ($booking->shipping_status == 'delivered' || $booking->shipping_status == 'taken') bg-primary @else bg-success @endif"
                                name="shipping_status"
                                onchange="document.getElementById('shippingStatusForm-{{ $booking->id }}').submit()">
                                <option value="pending" {{ $booking->shipping_status == 'pending' ? 'selected' : '' }}>
                                  {{ __('Pending') }}
                                </option>

                                @if ($booking->shipping_method == 'self pickup')
                                  <option value="taken" {{ $booking->shipping_status == 'taken' ? 'selected' : '' }}>
                                    {{ __('Taken') }}
                                  </option>
                                @else
                                  <option value="delivered"
                                    {{ $booking->shipping_status == 'delivered' ? 'selected' : '' }}>
                                    {{ __('Delivered') }}
                                  </option>
                                @endif

                                <option value="returned"
                                  {{ $booking->shipping_status == 'returned' ? 'selected' : '' }}>
                                  {{ __('Returned') }}
                                </option>
                              </select>
                            </form>
                          </td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                {{ __('Select') }}
                              </button>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{ route('vendor.equipment_booking.details', ['id' => $booking->id, 'language' => request()->input('language')]) }}"
                                  class="dropdown-item">
                                  {{ __('Details') }}
                                </a>

                                @if (!is_null($booking->attachment))
                                  <a href="#" class="dropdown-item" data-toggle="modal"
                                    data-target="#receiptModal-{{ $booking->id }}">
                                    {{ __('Receipt') }}
                                  </a>
                                @endif

                                @if (!is_null($booking->invoice))
                                  <a href="{{ asset('assets/file/invoices/equipment/' . $booking->invoice) }}"
                                    class="dropdown-item" target="_blank">
                                    {{ __('Invoice') }}
                                  </a>
                                @endif

                                <form class="deleteForm d-block"
                                  action="{{ route('vendor.equipment_booking.delete', ['id' => $booking->id]) }}"
                                  method="post">
                                  @csrf
                                  <button type="submit" class="deleteBtn">
                                    {{ __('Delete') }}
                                  </button>
                                </form>
                              </div>
                            </div>
                          </td>
                        </tr>

                        @includeWhen($booking->attachment, 'vendors.booking.show-receipt')
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="mt-3 text-center">
            <div class="d-inline-block mx-auto">
              {{ $bookings->appends([
                      'booking_no' => request()->input('booking_no'),
                      'payment_status' => request()->input('payment_status'),
                      'shipping_type' => request()->input('shipping_type'),
                      'shipping_status' => request()->input('shipping_status'),
                      'language' => request()->input('language'),
                  ])->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
