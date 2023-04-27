<div class="sidebar sidebar-style-2"

  data-background-color="{{ Session::get('vendor_theme_version') == 'light' ? 'white' : 'dark2' }}">

  <div class="sidebar-wrapper scrollbar scrollbar-inner">

    <div class="sidebar-content">

      <div class="user">

        <div class="avatar-sm float-left mr-2">

          @if (Auth::guard('vendor')->user()->photo != null)

            <!-- <img src="{{ asset('assets/admin/img/vendor-photo/' . Auth::guard('vendor')->user()->photo) }}"

              alt="Vendor Image" class="avatar-img rounded-circle"> -->
              <img src="" alt="Vendor Image" class="avatar-img rounded-circle">

          @else

            <img src="{{ asset('assets/img/blank-user.jpg') }}" alt="" class="avatar-img rounded-circle">

          @endif

        </div>



        <div class="info">

          <a data-toggle="collapse" href="#adminProfileMenu" aria-expanded="true">

            <span>

              {{ Auth::guard('vendor')->user()->username }}

              <span class="user-level">{{ __('Vendor') }}</span>

              <span class="caret"></span>

            </span>

          </a>



          <div class="clearfix"></div>



          <div class="collapse in" id="adminProfileMenu">

            <ul class="nav">

              <li>

                <a href="{{ route('vendor.edit.profile', ['language' => $defaultLang->code]) }}">

                  <span class="link-collapse">{{ __('Edit Profile') }}</span>

                </a>

              </li>



              <li>

                <a href="{{ route('vendor.change_password') }}">

                  <span class="link-collapse">{{ __('Change Password') }}</span>

                </a>

              </li>



              <li>

                <a href="{{ route('vendor.logout') }}">

                  <span class="link-collapse">{{ __('Logout') }}</span>

                </a>

              </li>

            </ul>

          </div>

        </div>

      </div>





      <ul class="nav nav-primary">

        {{-- search --}}

        <div class="row mb-3">

          <div class="col-12">

            <form>

              <div class="form-group py-0">

                <input name="term" type="text" class="form-control sidebar-search ltr"

                  placeholder="{{ __('Search Menu Here...') }}">

              </div>

            </form>

          </div>

        </div>



        {{-- dashboard --}}

        <li class="nav-item @if (request()->routeIs('vendor.dashboard')) active @endif">

          <a href="{{ route('vendor.dashboard') }}">

            <i class="la flaticon-paint-palette"></i>

            <p>{{ __('Dashboard') }}</p>

          </a>

        </li>



        <li

          class="nav-item @if (request()->routeIs('vendor.equipment_management.create_equipment')) active 

            @elseif (request()->routeIs('vendor.equipment_management.all_equipment')) active 

            @elseif (request()->routeIs('vendor.equipment_management.create_equipment')) active 

            @elseif (request()->routeIs('vendor.equipment_management.edit_equipment')) active @endif">

          <a data-toggle="collapse" href="#equipment">

            <i class="fal fa-truck-container"></i>

            <p>{{ __('Equipment') }}</p>

            <span class="caret"></span>

          </a>



          <div id="equipment"

            class="collapse 

              @if (request()->routeIs('vendor.equipment_management.create_equipment')) show 

              @elseif (request()->routeIs('vendor.equipment_management.all_equipment')) show 

              @elseif (request()->routeIs('vendor.equipment_management.create_equipment')) show 

              @elseif (request()->routeIs('vendor.equipment_management.edit_equipment')) show @endif">

            <ul class="nav nav-collapse">

              <li class="{{ request()->routeIs('vendor.equipment_management.create_equipment') ? 'active' : '' }}">

                <a href="{{ route('vendor.equipment_management.create_equipment') }}">

                  <span class="sub-item">{{ __('Add Equipment') }}</span>

                </a>

              </li>



              <li

                class="@if (request()->routeIs('vendor.equipment_management.all_equipment')) active 

                  @elseif (request()->routeIs('vendor.equipment_management.edit_equipment')) active @endif">

                <a href="{{ route('vendor.equipment_management.all_equipment', ['language' => $defaultLang->code]) }}">

                  <span class="sub-item">{{ __('All Equipment') }}</span>

                </a>

              </li>

            </ul>

          </div>

        </li>

        
        <li class="nav-item @if (request()->routeIs('vendor.edit.profile')) active @endif">

            <a href="{{ route('vendor.edit.profile', ['language' => $defaultLang->code]) }}">

              <i class="fal fa-user-edit"></i>

              <p>{{ __('Edit Profile') }}</p>

            </a>

        </li>

        <li class="nav-item @if (request()->routeIs('vendor.change_password')) active @endif">

            <a href="{{ route('vendor.change_password') }}">

              <i class="fal fa-key"></i>

              <p>{{ __('Change Password') }}</p>

            </a>

        </li>



        <li class="nav-item @if (request()->routeIs('vendor.logout')) active @endif">

            <a href="{{ route('vendor.logout') }}">

              <i class="fal fa-sign-out"></i>

              <p>{{ __('Logout') }}</p>

            </a>

        </li>


        <!-- <li

          class="nav-item @if (request()->routeIs('vendor.equipment_booking.settings.locations')) active 

            @elseif (request()->routeIs('vendor.equipment_booking.settings.shipping_methods'))  active @endif">

          <a data-toggle="collapse" href="#equipment_setting">

            <i class="fal fa-cog"></i>

            <p>{{ __('Delivery Settings') }}</p>

            <span class="caret"></span>

          </a>



          <div id="equipment_setting"

            class="collapse 

              @if (request()->routeIs('vendor.equipment_booking.settings.locations')) show 

              @elseif (request()->routeIs('vendor.equipment_booking.settings.shipping_methods')) show @endif">

            <ul class="nav nav-collapse">

              <li class="{{ request()->routeIs('vendor.equipment_booking.settings.locations') ? 'active' : '' }}">

                <a

                  href="{{ route('vendor.equipment_booking.settings.locations', ['language' => $defaultLang->code]) }}">

                  <span class="sub-item">{{ __('Locations') }}</span>

                </a>

              </li>



              <li

                class="{{ request()->routeIs('vendor.equipment_booking.settings.shipping_methods') ? 'active' : '' }}">

                <a href="{{ route('vendor.equipment_booking.settings.shipping_methods') }}">

                  <span class="sub-item">{{ __('Shipping Methods') }}</span>

                </a>

              </li>

            </ul>

          </div>

        </li>



        <li

          class="nav-item @if (request()->routeIs('vendor.equipment_booking.bookings')) active

          @elseif (request()->routeIs('vendor.equipment_booking.details')) active @endif">

          <a href="{{ route('vendor.equipment_booking.bookings', ['language' => $defaultLang->code]) }}">

            <i class="fal fa-calendar-check"></i>

            <p>{{ __('Equipment Bookings') }}</p>

          </a>

        </li>

        <li class="nav-item @if (request()->routeIs('vendor.equipment_booking.report')) active @endif">

          <a href="{{ route('vendor.equipment_booking.report') }}">

            <i class="fal fa-file-spreadsheet"></i>

            <p>{{ __('Booking Report') }}</p>

          </a>

        </li>



        <li

          class="nav-item @if (request()->routeIs('vendor.withdraw')) active 

            @elseif (request()->routeIs('vendor.withdraw.create'))  active @endif">

          <a data-toggle="collapse" href="#Withdrawals">

            <i class="fal fa-donate"></i>

            <p>{{ __('Withdrawals') }}</p>

            <span class="caret"></span>

          </a>



          <div id="Withdrawals"

            class="collapse 

              @if (request()->routeIs('vendor.withdraw')) show 

              @elseif (request()->routeIs('vendor.withdraw.create')) show @endif">

            <ul class="nav nav-collapse">

              <li class="{{ request()->routeIs('vendor.withdraw') ? 'active' : '' }}">

                <a href="{{ route('vendor.withdraw', ['language' => $defaultLang->code]) }}">

                  <span class="sub-item">{{ __('Withdrawal Requests') }}</span>

                </a>

              </li>



              <li class="{{ request()->routeIs('vendor.withdraw.create') ? 'active' : '' }}">

                <a href="{{ route('vendor.withdraw.create', ['language' => $defaultLang->code]) }}">

                  <span class="sub-item">{{ __('Make a Request') }}</span>

                </a>

              </li>

            </ul>

          </div>

        </li>



        <li class="nav-item @if (request()->routeIs('vendor.transcation')) active @endif">

          <a href="{{ route('vendor.transcation') }}">

            <i class="fal fa-exchange-alt"></i>

            <p>{{ __('Transactions') }}</p>

          </a>

        </li>

        @php

          $support_status = DB::table('support_ticket_statuses')->first();

        @endphp

        @if ($support_status->support_ticket_status == 'active')

          {{-- Support Ticket --}}

          <li

            class="nav-item @if (request()->routeIs('vendor.support_tickets')) active

            @elseif (request()->routeIs('vendor.support_tickets.message')) active

            @elseif (request()->routeIs('vendor.support_ticket.create')) active @endif">

            <a data-toggle="collapse" href="#support_ticket">

              <i class="la flaticon-web-1"></i>

              <p>{{ __('Support Tickets') }}</p>

              <span class="caret"></span>

            </a>



            <div id="support_ticket"

              class="collapse

              @if (request()->routeIs('vendor.support_tickets')) show

              @elseif (request()->routeIs('vendor.support_tickets.message')) show

              @elseif (request()->routeIs('vendor.support_ticket.create')) show @endif">

              <ul class="nav nav-collapse">



                <li

                  class="{{ request()->routeIs('vendor.support_tickets') && empty(request()->input('status')) ? 'active' : '' }}">

                  <a href="{{ route('vendor.support_tickets') }}">

                    <span class="sub-item">{{ __('All Tickets') }}</span>

                  </a>

                </li>

                <li class="{{ request()->routeIs('vendor.support_ticket.create') ? 'active' : '' }}">

                  <a href="{{ route('vendor.support_ticket.create') }}">

                    <span class="sub-item">{{ __('Add a Ticket') }}</span>

                  </a>

                </li>

              </ul>

            </div>

          </li>

        @endif





        <li class="nav-item @if (request()->routeIs('vendor.edit.profile')) active @endif">

          <a href="{{ route('vendor.edit.profile', ['language' => $defaultLang->code]) }}">

            <i class="fal fa-user-edit"></i>

            <p>{{ __('Edit Profile') }}</p>

          </a>

        </li>

        <li class="nav-item @if (request()->routeIs('vendor.change_password')) active @endif">

          <a href="{{ route('vendor.change_password') }}">

            <i class="fal fa-key"></i>

            <p>{{ __('Change Password') }}</p>

          </a>

        </li>



        <li class="nav-item @if (request()->routeIs('vendor.logout')) active @endif">

          <a href="{{ route('vendor.logout') }}">

            <i class="fal fa-sign-out"></i>

            <p>{{ __('Logout') }}</p>

          </a>

        </li> -->

      </ul>

    </div>

  </div>

</div>

