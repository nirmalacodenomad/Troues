<?php

namespace App\Http\Requests\Instrument;

use App\Models\Language;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Foundation\Http\FormRequest;

class EquipmentStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $data['perDayPrice'] = $this->per_day_price;
    $data['perWeekPrice'] = $this->per_week_price;
    $data['perMonthPrice'] = $this->per_month_price;

    $ruleArray = [
      'slider_images' => 'required',
      'thumbnail_image' => [
        'required',
        'dimensions:width=370,height=430',
        new ImageMimeTypeRule()
      ],
      'quantity' => 'required|numeric',
      'min_booking_days' => 'required|numeric',
      'max_booking_days' => 'required|numeric',
      'per_day_price' => function ($attribute, $value, $fail) use ($data) {
        if (
          empty($data['perDayPrice']) &&
          empty($data['perWeekPrice']) && empty($data['perMonthPrice'])
        ) {
          $fail('At least one price field is required.');
        }
      }
    ];

    $languages = Language::all();

    foreach ($languages as $language) {
      $ruleArray[$language->code . '_title'] = 'required|max:255|unique:equipment_contents,title';
      $ruleArray[$language->code . '_category_id'] = 'required';
      $ruleArray[$language->code . '_features'] = 'required';
      $ruleArray[$language->code . '_description'] = 'min:30';
    }

    return $ruleArray;
  }

  /**
   * Get the validation messages that apply to the request.
   *
   * @return array
   */
  public function messages()
  {
    $messageArray = [
      'min_booking_days.required' => 'The minimum booking days field is required.',
      'min_booking_days.numeric' => 'The minimum booking days must be a number.',
      'max_booking_days.required' => 'The maximum booking days field is required.',
      'max_booking_days.numeric' => 'The maximum booking days must be a number.',
    ];

    $languages = Language::all();

    foreach ($languages as $language) {
      $messageArray[$language->code . '_title.required'] = 'The title field is required for ' . $language->name . ' language.';

      $messageArray[$language->code . '_title.max'] = 'The title field cannot contain more than 255 characters for ' . $language->name . ' language.';

      $messageArray[$language->code . '_title.unique'] = 'The title field must be unique for ' . $language->name . ' language.';

      $messageArray[$language->code . '_category_id.required'] = 'The category field is required for ' . $language->name . ' language.';

      $messageArray[$language->code . '_features.required'] = 'The features field is required for ' . $language->name . ' language.';

      $messageArray[$language->code . '_description.min'] = 'The description must be at least 30 characters for ' . $language->name . ' language.';
    }

    return $messageArray;
  }
}
