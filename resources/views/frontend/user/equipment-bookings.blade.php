@extends('frontend.layout')

@section('pageHeading')
  {{ __('Equipment Bookings') }}
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => __('Equipment Bookings'),
  ])

  <!--====== Start Equipment Bookings Section ======-->
  <section class="user-dashboard pt-130 pb-120">
    <div class="container">
      <div class="row">
        @includeIf('frontend.user.side-navbar')

        <div class="col-lg-9">
          <div class="row">
            <div class="col-lg-12">
              <div class="user-profile-details">
                <div class="account-info">
                  <div class="title">
                    <h4>{{ __('Booking List') }}</h4>
                  </div>

                  <div class="main-info">
                    @if (count($bookings) == 0)
                      <div class="row text-center mt-2">
                        <div class="col">
                          <h4>{{ __('No Booking Found') . '!' }}</h4>
                        </div>
                      </div>
                    @else
                      <div class="main-table">
                        <div class="table-responsive">
                          <table id="user-datatable"
                            class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4 w-100">
                            <thead>
                              <tr>
                                <th>{{ __('Booking Number') }}</th>
                                <th>{{ __('Vendor') }}</th>
                                <th>{{ __('Equipment') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Payment Status') }}</th>
                                <th>{{ __('Action') }}</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($bookings as $booking)
                                <tr>
                                  <td>{{ '#' . $booking->booking_number }}</td>
                                  <td>
                                    @php
                                      $vendor = $booking->vendor()->first();
                                    @endphp
                                    @if ($vendor)
                                      <a class="text-primary" target="_blank"
                                        href="{{ route('frontend.vendor.details', $vendor->username) }}">{{ $vendor->username }}</a>
                                    @else
                                      <span class="badge badge-success">{{ __('Admin') }}</span>
                                    @endif
                                  </td>
                                  <td>
                                    <a class="text-primary" target="_blank"
                                      href="{{ route('equipment_details', ['slug' => $booking->equipmentInfo->slug]) }}"
                                      target="_blank">
                                      {{ strlen($booking->equipmentInfo->title) > 20 ? mb_substr($booking->equipmentInfo->title, 0, 20, 'UTF-8') . '...' : $booking->equipmentInfo->title }}
                                    </a>
                                  </td>
                                  <td>{{ date_format($booking->created_at, 'M d, Y') }}</td>
                                  <td>
                                    @if ($booking->payment_status == 'completed')
                                      <span
                                        class="completed {{ $currentLanguageInfo->direction == 1 ? 'mr-2' : 'ml-2' }}">{{ __('Completed') }}</span>
                                    @elseif ($booking->payment_status == 'pending')
                                      <span
                                        class="pending {{ $currentLanguageInfo->direction == 1 ? 'mr-2' : 'ml-2' }}">{{ __('Pending') }}</span>
                                    @else
                                      <span
                                        class="rejected {{ $currentLanguageInfo->direction == 1 ? 'mr-2' : 'ml-2' }}">{{ __('Rejected') }}</span>
                                    @endif
                                  </td>
                                  <td>
                                    <a href="{{ route('user.equipment_booking.details', ['id' => $booking->id]) }}"
                                      class="btn">{{ __('Details') }}</a>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--====== End Equipment Bookings Section ======-->
@endsection
