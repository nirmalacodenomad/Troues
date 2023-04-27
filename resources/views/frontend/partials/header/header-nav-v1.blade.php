<div class="header-navigation">
  <div class="container-fluid pl-0 pr-0">
    <div class="primary-menu d-flex align-items-center justify-content-between">
      <div class="site-branding">
      <img data-src="" alt="Troues" class="lazy">
          </a>
        <!-- @if (!empty($websiteInfo->logo))
          <a href="{{ route('index') }}" class="brand-logo">
            <img data-src="{{ asset('assets/img/' . $websiteInfo->logo) }}" alt="website logo" class="lazy">
          </a>
        @endif -->
      </div>

      <div class="nav-menu">
        <div class="navbar-close"><i class="fal fa-times"></i></div>

        <nav class="main-menu">
          <ul>
            <li class="menu-item">
              <a href="">Home</a>
            </li>
            <li class="menu-item">
              <a href="">Equipment</a>
            </li>
            <li class="menu-item">
              <a href="">Vendors</a>
            </li>
            <li class="menu-item">
              <a href="">FAQ</a>
            </li>
            <li class="menu-item">
              <a href="">Contact</a>
            </li>
          </ul>
          <!-- <ul>
            @php $menuDatas = json_decode($menuInfos); @endphp
            @foreach ($menuDatas as $menuData)
              @php $href = get_href($menuData); @endphp

              @if (!property_exists($menuData, 'children'))
                <li class="menu-item">
                  <a href="{{ $href }}">{{ $menuData->text }}</a>
                </li>
              @else
                <li class="menu-item menu-item-has-children">
                  <a href="{{ $href }}">{{ $menuData->text }}</a>
                  <ul class="sub-menu">
                    @php $childMenuDatas = $menuData->children; @endphp

                    @foreach ($childMenuDatas as $childMenuData)
                      @php $child_href = get_href($childMenuData); @endphp

                      <li><a href="{{ $child_href }}">{{ $childMenuData->text }}</a></li>
                    @endforeach
                  </ul>
                </li>
              @endif
            @endforeach
          </ul> -->
        </nav>
      </div>

      <div class="navbar-toggler">
        <span></span><span></span><span></span>
      </div>

      <div class="header-right-nav">
        @if (count($socialMediaInfos) > 0)
          <div class="social-box">
            <ul class="social-link">
              @foreach ($socialMediaInfos as $socialMediaInfo)
                <li>
                  <a href="{{ $socialMediaInfo->url }}" target="_blank">
                    <i class="{{ $socialMediaInfo->icon }}"></i>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
