<div class="col-lg-3">
  <div class="user-sidebar">
    <ul class="links">
      <li><a href="{{ route('user.dashboard') }}"
          class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">{{ __('Dashboard') }}</a></li>

      <li><a href="{{ route('user.equipment_bookings') }}"
          class="{{ request()->routeIs('user.equipment_bookings') || request()->routeIs('user.equipment_booking.details') ? 'active' : '' }}">{{ __('Equipment Bookings') }}</a>
      </li>

      <li><a href="{{ route('user.product_orders') }}"
          class="{{ request()->routeIs('user.product_orders') || request()->routeIs('user.product_order.details') ? 'active' : '' }}">{{ __('Product Orders') }}</a>
      </li>
      <li><a href="{{ route('user.edit_profile') }}"
          class="{{ request()->routeIs('user.edit_profile') ? 'active' : '' }}">{{ __('Edit Profile') }}</a></li>

      <li><a href="{{ route('user.change_password') }}"
          class="{{ request()->routeIs('user.change_password') ? 'active' : '' }}">{{ __('Change Password') }}</a>
      </li>

      <li><a href="{{ route('user.logout') }}">{{ __('Logout') }}</a></li>
    </ul>
  </div>
</div>
