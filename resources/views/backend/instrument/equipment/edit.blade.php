@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Edit Equipment') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Equipment') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('All Equipment') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Edit Equipment') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Edit Equipment') }}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ route('admin.equipment_management.all_equipment', ['language' => $defaultLang->code]) }}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            {{ __('Back') }}
          </a>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 offset-lg-2">
              <div class="alert alert-danger pb-1 dis-none" id="equipmentErrors">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <ul></ul>
              </div>

              <div class="ml-2">
                <label for=""><strong>{{ __('Slider Images') . '*' }}</strong></label>

                @php $sliderImages = json_decode($equipment->slider_images); @endphp

                @if (count($sliderImages) > 0)
                  <div id="reload-slider-div">
                    <div class="row mt-2">
                      <div class="col">
                        <table class="table" id="img-table">
                          @foreach ($sliderImages as $key => $sliderImage)
                            <tr class="table-row" id="{{ 'slider-image-' . $key }}">
                              <td>
                                <img class="thumb-preview wf-150"
                                  src="{{ asset('assets/img/equipments/slider-images/' . $sliderImage) }}"
                                  alt="slider image">
                              </td>
                              <td>
                                <i class="fa fa-times-circle"
                                  onclick="rmvStoredImg({{ $equipment->id }}, {{ $key }})"></i>
                              </td>
                            </tr>
                          @endforeach
                        </table>
                      </div>
                    </div>
                  </div>
                @endif

                <form id="slider-dropzone" enctype="multipart/form-data" class="dropzone mt-2 mb-0">
                  @csrf
                  <div class="fallback"></div>
                </form>
                <p class="em text-danger mt-3 mb-0" id="err_slider_image"></p>
              </div>

              <form id="equipmentForm"
                action="{{ route('admin.equipment_management.update_equipment', ['id' => $equipment->id]) }}"
                enctype="multipart/form-data" method="POST">
                @csrf
                <div id="slider-image-id"></div>

                <div class="form-group">
                  <label for="">{{ __('Thumbnail Image') . '*' }}</label>
                  <br>
                  <div class="thumb-preview">
                    <img src="{{ asset('assets/img/equipments/thumbnail-images/' . $equipment->thumbnail_image) }}"
                      alt="image" class="uploaded-img">
                  </div>

                  <div class="mt-3">
                    <div role="button" class="btn btn-primary btn-sm upload-btn">
                      {{ __('Choose Image') }}
                      <input type="file" class="img-input" name="thumbnail_image">
                    </div>
                  </div>
                  <p class="text-warning">{{ __('Image Size: 370x430') }}</p>
                </div>

                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Quantity') . '*' }}</label>
                      <input type="number" class="form-control" name="quantity" placeholder="{{ __('Enter Quantity') }}"
                        value="{{ $equipment->quantity }}">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Minimum Booking Days') . '*' }}</label>
                      <input type="number" class="form-control" name="min_booking_days"
                        placeholder="{{ __('Enter Number of Days') }}" value="{{ $equipment->min_booking_days }}">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Maximum Booking Days') . '*' }}</label>
                      <input type="number" class="form-control" name="max_booking_days"
                        placeholder="{{ __('Enter Number of Days') }}" value="{{ $equipment->max_booking_days }}">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Offer') . ' (' . __('in Percentage') . ')' }}</label>
                      <input type="number" class="form-control" name="offer"
                        placeholder="{{ __('Enter Offer Amount') }}" value="{{ $equipment->offer }}">
                    </div>
                  </div>
                </div>

                <div class="row">
                  @php $currencyText = $currencyInfo->base_currency_text; @endphp

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Per Day Price') . ' (' . $currencyText . ')' }}</label>
                      <input type="number" class="form-control" name="per_day_price"
                        placeholder="{{ __('Enter Per Day Price') }}" value="{{ $equipment->per_day_price }}">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Per Week Price') . ' (' . $currencyText . ')' }}</label>
                      <input type="number" class="form-control" name="per_week_price"
                        placeholder="{{ __('Enter Per Week Price') }}" value="{{ $equipment->per_week_price }}">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Per Month Price') . ' (' . $currencyText . ')' }}</label>
                      <input type="number" class="form-control" name="per_month_price"
                        placeholder="{{ __('Enter Per Month Price') }}" value="{{ $equipment->per_month_price }}">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>{{ __('Vendor') }}</label>
                      <select name="vendor_id" id="" class="select2">
                        <option value="">{{ __('Please Select') }} </option>
                        @foreach ($vendors as $item)
                          <option {{ $item->id == $equipment->vendor_id ? 'selected' : '' }}
                            value="{{ $item->id }}">
                            {{ $item->username }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div id="accordion" class="mt-5">
                  @foreach ($languages as $language)
                    @php $equipmentData = $language->equipmentData; @endphp

                    <div class="version">
                      <div class="version-header" id="heading{{ $language->id }}">
                        <h5 class="mb-0">
                          <button type="button"
                            class="btn btn-link {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                            data-toggle="collapse" data-target="#collapse{{ $language->id }}"
                            aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $language->id }}">
                            {{ $language->name . __(' Language') }} {{ $language->is_default == 1 ? '(Default)' : '' }}
                          </button>
                        </h5>
                      </div>

                      <div id="collapse{{ $language->id }}"
                        class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                        <div class="version-body">
                          <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Title') . '*' }}</label>
                                <input type="text" class="form-control" name="{{ $language->code }}_title"
                                  placeholder="{{ __('Enter Title') }}"
                                  value="{{ is_null($equipmentData) ? '' : $equipmentData->title }}">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                @php $categories = $language->categories; @endphp

                                <label>{{ __('Category') . '*' }}</label>
                                <select name="{{ $language->code }}_category_id" class="form-control">
                                  @if (empty($categories))
                                    <option selected disabled>{{ __('Select a Category') }}</option>
                                  @else
                                    <option selected disabled>{{ __('Select a Category') }}</option>

                                    @foreach ($categories as $category)
                                      <option value="{{ $category->id }}"
                                        {{ !empty($equipmentData) && $category->id == $equipmentData->equipment_category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                      </option>
                                    @endforeach
                                  @endif
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Features') . '*' }}</label>
                                <textarea class="form-control" name="{{ $language->code }}_features"
                                  placeholder="{{ __('Enter Equipment Features') }}" rows="7">{{ is_null($equipmentData) ? '' : $equipmentData->features }}</textarea>
                                <p class="text-warning mt-1 mb-0">
                                  {{ __('To seperate the features, enter a new line after each feature.') }}
                                </p>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Description') . '*' }}</label>
                                <textarea class="form-control summernote" name="{{ $language->code }}_description"
                                  placeholder="{{ __('Enter Equipment Description') }}" data-height="300">{{ is_null($equipmentData) ? '' : replaceBaseUrl($equipmentData->description, 'summernote') }}</textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Meta Keywords') }}</label>
                                <input class="form-control" name="{{ $language->code }}_meta_keywords"
                                  placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput"
                                  value="{{ is_null($equipmentData) ? '' : $equipmentData->meta_keywords }}">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col">
                              <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                <label>{{ __('Meta Description') }}</label>
                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                  placeholder="{{ __('Enter Meta Description') }}">{{ is_null($equipmentData) ? '' : $equipmentData->meta_description }}</textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="equipmentForm" class="btn btn-success">
                {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    'use strict';
    const imgUpUrl = "{{ route('admin.equipment_management.upload_slider_image') }}";
    const imgRmvUrl = "{{ route('admin.equipment_management.remove_slider_image') }}";
    const imgDetachUrl = "{{ route('admin.equipment_management.detach_slider_image') }}";
  </script>

  <script type="text/javascript" src="{{ asset('assets/js/slider-image.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/admin-partial.js') }}"></script>
@endsection
