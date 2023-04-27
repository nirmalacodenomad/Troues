<!DOCTYPE html>
<html lang="{{ $currentLanguageInfo->code }}" @if ($currentLanguageInfo->direction == 1) dir="rtl" @endif>

<head>
  {{-- required meta tags --}}
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  {{-- csrf-token for ajax request --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- title --}}
  <title>@yield('pageHeading') {{ '| ' . $websiteInfo->website_title }}</title>

  <meta name="keywords" content="@yield('metaKeywords')">
  <meta name="description" content="@yield('metaDescription')">
  <meta name="theme-color" content="{{ $basicInfo->primary_color }}" />

  {{-- fav icon --}}
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/' . $websiteInfo->favicon) }}">
  <link rel="apple-touch-icon" href="{{ asset('assets/img/' . $websiteInfo->favicon) }}">

  {{-- include styles --}}
  @includeIf('frontend.partials.styles')
  <link rel="manifest" crossorigin="use-credentials" href="{{ asset('manifest.json') }}" />

  {{-- additional style --}}
  @yield('style')
</head>

<body>
  {{-- preloader start --}}
  <div class="preloader">
    <div class="lds-ellipsis">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  {{-- preloader end --}}

  {{-- header start --}}
  @if ($basicInfo->theme_version == 1)
    <header class="header-area-one">
      {{-- include header-top --}}
      @includeIf('frontend.partials.header.header-top-v1')

      {{-- include header-nav --}}
      @includeIf('frontend.partials.header.header-nav-v1')
    </header>
  @else
    <header class="header-area-two">
      {{-- include header-top --}}
      @includeIf('frontend.partials.header.header-top-v2')

      {{-- include header-nav --}}
      @includeIf('frontend.partials.header.header-nav-v2')
    </header>
  @endif
  {{-- header end --}}

  @yield('content')

  {{-- back to top start --}}
  <a href="#" class="back-to-top"><i class="fas fa-angle-up"></i></a>
  {{-- back to top end --}}

  {{-- floating whatsapp button --}}
  @if ($basicInfo->whatsapp_status == 1 && $basicInfo->tawkto_status == 0)
    <div class="whatsapp-btn"></div>
  @endif

  {{-- tawk.to button --}}
  @if ($basicInfo->whatsapp_status == 0 && $basicInfo->tawkto_status == 1)
    @php
      $directLink = str_replace('tawk.to', 'embed.tawk.to', $basicInfo->tawkto_direct_chat_link);
      $directLink = str_replace('chat/', '', $directLink);
    @endphp

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
      "use strict";

      var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();

      (function() {
        var s1 = document.createElement("script"),
          s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = '{{ $directLink }}';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
      })();
    </script>
    <!--End of Tawk.to Script-->
  @endif

  {{-- announcement popup --}}
  @includeIf('frontend.partials.popups')

  {{-- cookie alert --}}
  @if (!is_null($cookieAlertInfo) && $cookieAlertInfo->cookie_alert_status == 1)
    @include('cookie-consent::index')
  @endif

  {{-- include footer --}}
  @if ($basicInfo->theme_version == 1)
    @includeIf('frontend.partials.footer.footer-v1')
  @else
    @includeIf('frontend.partials.footer.footer-v2')
  @endif

  {{-- include scripts --}}
  @includeIf('frontend.partials.scripts')

  {{-- additional script --}}
  @yield('script')
</body>

</html>
