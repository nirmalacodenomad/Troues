<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomePage\ProductSection;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $language = Language::query()->where('code', '=', $request->language)->first();
    $information['language'] = $language;

    $information['data'] = $language->productSection()->first();

    $information['langs'] = Language::all();

    return view('backend.home-page.product-section', $information);
  }

  public function updateSectionInfo(Request $request)
  {
    $language = Language::query()->where('code', '=', $request->language)->first();

    ProductSection::query()->updateOrCreate(
      ['language_id' => $language->id],
      [
        'subtitle' => $request->subtitle,
        'title' => $request->title,
        'text' => $request->text
      ]
    );

    Session::flash('success', 'Product section updated successfully!');

    return redirect()->back();
  }
}
