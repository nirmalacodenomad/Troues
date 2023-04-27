<?php

namespace App\Http\Controllers\BackEnd\Instrument;

use App\Http\Controllers\Controller;
use App\Models\Instrument\EquipmentCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
  public function index(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the equipment categories of that language from db
    $information['categories'] = $language->equipmentCategory()->orderByDesc('id')->get();

    // also, get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.instrument.category.index', $information);
  }

  public function store(Request $request)
  {
    $rules = [
      'language_id' => 'required',
      'name' => 'required|unique:equipment_categories|max:255',
      'status' => 'required|numeric',
      'serial_number' => 'required|numeric'
    ];

    $message = [
      'language_id.required' => 'The language field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $message);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()
      ], 400);
    }

    EquipmentCategory::create($request->except('slug') + [
      'slug' => createSlug($request->name)
    ]);

    Session::flash('success', 'New equipment category added successfully!');

    return Response::json(['status' => 'success'], 200);
  }

  public function update(Request $request)
  {
    $rules = [
      'name' => [
        'required',
        'max:255',
        Rule::unique('equipment_categories', 'name')->ignore($request->id, 'id')
      ],
      'status' => 'required|numeric',
      'serial_number' => 'required|numeric'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()
      ], 400);
    }

    $category = EquipmentCategory::find($request->id);

    $category->update($request->except('slug') + [
      'slug' => createSlug($request->name)
    ]);

    Session::flash('success', 'Equipment category updated successfully!');

    return Response::json(['status' => 'success'], 200);
  }

  public function destroy($id)
  {
    $category = EquipmentCategory::find($id);
    $equipmentContents = $category->equipmentContent()->get();

    if (count($equipmentContents) > 0) {
      return redirect()->back()->with('warning', 'First delete all the equipments of this category!');
    } else {
      $category->delete();

      return redirect()->back()->with('success', 'Category deleted successfully!');
    }
  }

  public function bulkDestroy(Request $request)
  {
    $ids = $request->ids;

    $errorOccured = false;

    foreach ($ids as $id) {
      $category = EquipmentCategory::find($id);
      $equipmentContents = $category->equipmentContent()->get();

      if (count($equipmentContents) > 0) {
        $errorOccured = true;
        break;
      } else {
        $category->delete();
      }
    }

    if ($errorOccured == true) {
      Session::flash('warning', 'First delete all the equipment of these categories!');
    } else {
      Session::flash('success', 'Equipment categories deleted successfully!');
    }

    return Response::json(['status' => 'success'], 200);
  }
}
