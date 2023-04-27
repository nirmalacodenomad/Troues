<?php

namespace App\Http\Controllers\BackEnd\Instrument;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Instrument\Coupon;
use App\Models\Instrument\Equipment;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
  public function index()
  {
    // get the coupons from db
    $information['coupons'] = Coupon::orderByDesc('id')->get();

    // also, get the currency information from db
    $information['currencyInfo'] = $this->getCurrencyInfo();

    $language = Language::query()->where('is_default', '=', 1)->first();

    $allEquipment = Equipment::all();

    $allEquipment->map(function ($equipment) use ($language) {
      $equipment['title'] = $equipment->content()->where('language_id', $language->id)->pluck('title')->first();
    });

    $information['allEquipment'] = $allEquipment;

    return view('backend.instrument.coupon.index', $information);
  }

  public function store(CouponRequest $request)
  {
    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);

    if ($request->filled('equipments')) {
      $equipments = $request->equipments;
    }

    Coupon::create($request->except('start_date', 'end_date', 'equipments') + [
      'start_date' => date_format($startDate, 'Y-m-d'),
      'end_date' => date_format($endDate, 'Y-m-d'),
      'equipments' => isset($equipments) ? json_encode($equipments) : null
    ]);

    Session::flash('success', 'New coupon added successfully!');

    return response()->json(['status' => 'success'], 200);
  }

  public function update(CouponRequest $request)
  {
    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);

    if ($request->filled('equipments')) {
      $equipments = $request->equipments;
    }

    Coupon::find($request->id)->update($request->except('start_date', 'end_date', 'equipments') + [
      'start_date' => date_format($startDate, 'Y-m-d'),
      'end_date' => date_format($endDate, 'Y-m-d'),
      'equipments' => isset($equipments) ? json_encode($equipments) : null
    ]);

    Session::flash('success', 'Coupon updated successfully!');

    return response()->json(['status' => 'success'], 200);
  }

  public function destroy($id)
  {
    Coupon::find($id)->delete();

    return redirect()->back()->with('success', 'Coupon deleted successfully!');
  }
}
