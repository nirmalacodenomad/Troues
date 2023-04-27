{{-- fontawesome css --}}
<link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">

{{-- bootstrap css --}}
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

{{-- magnific-popup css --}}
<link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">

{{-- slick css --}}
<link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">

{{-- slick theme css --}}
<link rel="stylesheet" href="{{ asset('assets/css/slick-theme.css') }}">

{{-- toastr css --}}
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

{{-- datatables css --}}
<link rel="stylesheet" href="{{ asset('assets/css/datatables-1.10.23.min.css') }}">

{{-- datatables bootstrap css --}}
<link rel="stylesheet" href="{{ asset('assets/css/datatables.bootstrap4.min.css') }}">

{{-- jQuery-ui css --}}
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">

{{-- date-range-picker css --}}
<link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.min.css') }}">

{{-- default css --}}
<link rel="stylesheet" href="{{ asset('assets/css/default.min.css') }}">

{{-- whatsapp css --}}
<link rel="stylesheet" href="{{ asset('assets/css/floating-whatsapp.css') }}">

{{-- main css --}}
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

{{-- responsive css --}}
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

{{-- Update css --}}
<link rel="stylesheet" href="{{ asset('assets/css/update.css') }}">

@if ($currentLanguageInfo->direction == 1)
  {{-- right-to-left css --}}
  <link rel="stylesheet" href="{{ asset('assets/css/rtl.css') }}">

  {{-- right-to-left-responsive css --}}
  <link rel="stylesheet" href="{{ asset('assets/css/rtl-responsive.css') }}">
@endif

@php
  $primaryColor = $basicInfo->primary_color;
  $secondaryColor = $basicInfo->secondary_color;
  $breadcrumbOverlayColor = $basicInfo->breadcrumb_overlay_color;
@endphp
<link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">

{{-- website-color css using a php file --}}
<link rel="stylesheet"
  href="{{ asset("assets/css/website-color.php?primary_color=$primaryColor&secondary_color=$secondaryColor&breadcrumb_overlay_color=$breadcrumbOverlayColor") }}">
