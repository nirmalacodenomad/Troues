<?php



namespace App\Http\Controllers\Vendor;



use App\Http\Controllers\Controller;

use App\Http\Helpers\UploadFile;

use App\Http\Requests\Instrument\EquipmentStoreRequest;

use App\Http\Requests\Instrument\EquipmentUpdateRequest;

use App\Models\Instrument\Equipment;

use App\Models\Instrument\EquipmentContent;

use App\Models\Language;

use App\Rules\ImageMimeTypeRule;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;

use Mews\Purifier\Facades\Purifier;



class EquipmentController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $language = Language::where('code', $request->language)->first();



        $information['langs'] = Language::all();



        $information['allEquipment'] = Equipment::query()->where('vendor_id', Auth::guard('vendor')->user()->id)->join('equipment_contents', 'equipments.id', '=', 'equipment_contents.equipment_id')

            ->join('equipment_categories', 'equipment_categories.id', '=', 'equipment_contents.equipment_category_id')

            ->where('equipment_contents.language_id', '=', $language->id)

            ->select('equipments.id', 'equipments.thumbnail_image', 'equipments.quantity', 'equipment_contents.title', 'equipment_contents.slug', 'equipment_categories.name as categoryName', 'equipments.is_featured')

            ->orderByDesc('equipments.id')

            ->get();



        return view('vendors.equipment.index', $information);

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $information['currencyInfo'] = $this->getCurrencyInfo();



        $languages = Language::all();



        $languages->map(function ($language) {

            $language['categories'] = $language->equipmentCategory()->where('status', 1)->orderByDesc('id')->get();

        });



        $information['languages'] = $languages;



        return view('vendors.equipment.create', $information);

    }



    /**

     * Store a new slider image in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function uploadImage(Request $request)

    {

        $rules = [

            'slider_image' => new ImageMimeTypeRule()

        ];



        $validator = Validator::make($request->all(), $rules);



        if ($validator->fails()) {

            return Response::json([

                'error' => $validator->getMessageBag()->toArray()

            ], 400);

        }



        $imageName = UploadFile::store(public_path('assets/img/equipments/slider-images/'), $request->file('slider_image'));



        return Response::json(['uniqueName' => $imageName], 200);

    }



    /**

     * Remove a slider image from storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function removeImage(Request $request)

    {

        if (empty($request['imageName'])) {

            return Response::json(['error' => 'The request has no file name.'], 400);

        } else {

            @unlink(public_path('assets/img/equipments/slider-images/') . $request['imageName']);



            return Response::json(['success' => 'The file has been deleted.'], 200);

        }

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(EquipmentStoreRequest $request)

    {
   
        // store thumbnail image in storage

        $thumbnailImgName = UploadFile::store(public_path('assets/img/equipments/thumbnail-images/'), $request->file('thumbnail_image'));



        // get the lowest price

        $priceBtnStatus = $request['price_btn_status'];



        if ($priceBtnStatus == 0) {

            $prices = [];



            array_push($prices, $request->per_day_price);

            array_push($prices, $request->per_week_price);

            array_push($prices, $request->per_month_price);



            $priceArr = array_diff($prices, array(null));

            $lowestPrice = min($priceArr);

        }



        // store data in db

        $equipment = Equipment::create($request->except('thumbnail_image', 'slider_images', 'lowest_price') + [

            'thumbnail_image' => $thumbnailImgName,

            'vendor_id' => Auth::guard('vendor')->user()->id,

            'slider_images' => json_encode($request['slider_images']),

            'lowest_price' => $priceBtnStatus == 0 ? $lowestPrice : null

        ]);



        $languages = Language::all();



        foreach ($languages as $language) {

            $equipmentContent = new EquipmentContent();

            $equipmentContent->language_id = $language->id;

            $equipmentContent->equipment_category_id = $request[$language->code . '_category_id'];

            $equipmentContent->equipment_id = $equipment->id;

            $equipmentContent->title = $request[$language->code . '_title'];

            $equipmentContent->slug = createSlug($request[$language->code . '_title']);

            $equipmentContent->features = $request[$language->code . '_features'];

            $equipmentContent->description = Purifier::clean($request[$language->code . '_description']);

            $equipmentContent->meta_keywords = $request[$language->code . '_meta_keywords'];

            $equipmentContent->meta_description = $request[$language->code . '_meta_description'];

            $equipmentContent->save();

        }



        Session::flash('success', 'New equipment added successfully!');



        return Response::json(['status' => 'success'], 200);

    }



    /**

     * Update featured status of a specified resource.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function updateFeatured(Request $request, $id)

    {

        $equipment = Equipment::find($id);



        if ($request['is_featured'] == 'yes') {

            $equipment->update(['is_featured' => 'yes']);



            Session::flash('success', 'Equipment featured successfully!');

        } else {

            $equipment->update(['is_featured' => 'no']);



            Session::flash('success', 'Equipment unfeatured successfully!');

        }



        return redirect()->back();

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $equipment = Equipment::find($id);

        if ($equipment) {

            if ($equipment->vendor_id != Auth::guard('vendor')->user()->id) {

                return redirect()->route('vendor.dashboard');

            }

        } else {

            return redirect()->route('vendor.dashboard');

        }





        $information['equipment'] = $equipment;



        // get the currency information from db

        $information['currencyInfo'] = $this->getCurrencyInfo();



        // get all the languages from db

        $languages = Language::all();



        $languages->map(function ($language) use ($equipment) {

            // get equipment information of each language from db

            $language['equipmentData'] = $language->equipmentContent()->where('equipment_id', $equipment->id)->first();



            // get all the categories of each language from db

            $language['categories'] = $language->equipmentCategory()->where('status', 1)->orderByDesc('id')->get();

        });



        $information['languages'] = $languages;



        return view('vendors.equipment.edit', $information);

    }



    /**

     * Remove 'stored' slider image form storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function detachImage(Request $request)

    {

        $id = $request['id'];

        $key = $request['key'];



        $equipment = Equipment::find($id);



        if (empty($equipment)) {

            return Response::json(['message' => 'Equipment not found!'], 400);

        } else {

            $sliderImages = json_decode($equipment->slider_images);



            if (count($sliderImages) == 1) {

                return Response::json(['message' => 'Sorry, the last image cannot be delete.'], 400);

            } else {

                $image = $sliderImages[$key];



                @unlink(public_path('assets/img/equipments/slider-images/') . $image);



                array_splice($sliderImages, $key, 1);



                $equipment->update([

                    'slider_images' => json_encode($sliderImages)

                ]);



                return Response::json(['message' => 'Slider image removed successfully!'], 200);

            }

        }

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(EquipmentUpdateRequest $request, $id)

    {

        $equipment = Equipment::find($id);



        // store thumbnail image in storage

        if ($request->hasFile('thumbnail_image')) {

            $newImage = $request->file('thumbnail_image');

            $oldImage = $equipment->thumbnail_image;

            $thumbnailImgName = UploadFile::update(public_path('assets/img/equipments/thumbnail-images/'), $newImage, $oldImage);

        }



        // merge slider images with existing images if request has new slider image

        if ($request->filled('slider_images')) {

            $prevImages = json_decode($equipment->slider_images);

            $newImages = $request['slider_images'];

            $imgArr = array_merge($prevImages, $newImages);

        }



        // get the lowest price

        $priceBtnStatus = $request['price_btn_status'];



        if ($priceBtnStatus == 0) {

            $prices = [];



            array_push($prices, $request->per_day_price);

            array_push($prices, $request->per_week_price);

            array_push($prices, $request->per_month_price);



            $priceArr = array_diff($prices, array(null));

            $lowestPrice = min($priceArr);

        }



        // store data in db

        $equipment->update($request->except('thumbnail_image', 'slider_images', 'lowest_price') + [

            'thumbnail_image' => $request->hasFile('thumbnail_image') ? $thumbnailImgName : $equipment->thumbnail_image,

            'vendor_id' => Auth::guard('vendor')->user()->id,

            'slider_images' => isset($imgArr) ? json_encode($imgArr) : $equipment->slider_images,

            'lowest_price' => $priceBtnStatus == 0 ? $lowestPrice : null

        ]);



        $languages = Language::all();



        foreach ($languages as $language) {

            $equipmentContent = EquipmentContent::where('equipment_id', $id)->where('language_id', $language->id)->first();



            if ($equipmentContent) {

                $equipmentContent->update([

                    'equipment_category_id' => $request[$language->code . '_category_id'],

                    'title' => $request[$language->code . '_title'],

                    'slug' => createSlug($request[$language->code . '_title']),

                    'features' => $request[$language->code . '_features'],

                    'description' => Purifier::clean($request[$language->code . '_description']),

                    'meta_keywords' => $request[$language->code . '_meta_keywords'],

                    'meta_description' => $request[$language->code . '_meta_description']

                ]);

            } else {

                EquipmentContent::create([

                    'language_id' => $language->id,

                    'equipment_id' => $equipment->id,

                    'equipment_category_id' => $request[$language->code . '_category_id'],

                    'title' => $request[$language->code . '_title'],

                    'slug' => createSlug($request[$language->code . '_title']),

                    'features' => $request[$language->code . '_features'],

                    'description' => Purifier::clean($request[$language->code . '_description']),

                    'meta_keywords' => $request[$language->code . '_meta_keywords'],

                    'meta_description' => $request[$language->code . '_meta_description']

                ]);

            }

        }



        Session::flash('success', 'Equipment updated successfully!');



        return Response::json(['status' => 'success'], 200);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $equipment = Equipment::find($id);



        // delete the thumbnail image

        @unlink(public_path('assets/img/equipments/thumbnail-images/') . $equipment->thumbnail_image);



        // delete the slider images

        $sliderImages = json_decode($equipment->slider_images);



        foreach ($sliderImages as $sliderImage) {

            @unlink(public_path('assets/img/equipments/slider-images/') . $sliderImage);

        }



        // delete the equipment contents

        $equipmentContents = $equipment->content()->get();



        foreach ($equipmentContents as $equipmentContent) {

            $equipmentContent->delete();

        }



        // delete all the bookings of this equipment

        $bookings = $equipment->booking()->get();



        if (count($bookings) > 0) {

            foreach ($bookings as $booking) {

                @unlink(public_path('assets/file/attachments/equipment/') . $booking->attachment);



                @unlink(public_path('assets/file/invoices/equipment/') . $booking->invoice);



                $booking->delete();

            }

        }



        // delete all the reviews of this equipment

        $equipmentReviews = $equipment->review()->get();



        if (count($equipmentReviews) > 0) {

            foreach ($equipmentReviews as $equipmentReview) {

                $equipmentReview->delete();

            }

        }



        $equipment->delete();



        return redirect()->back()->with('success', 'Equipment deleted successfully!');

    }



    /**

     * Remove the selected or all resources from storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function bulkDestroy(Request $request)

    {

        $ids = $request->ids;



        foreach ($ids as $id) {

            $equipment = Equipment::find($id);



            // delete the thumbnail image

            @unlink(public_path('assets/img/equipments/thumbnail-images/') . $equipment->thumbnail_image);



            // delete the slider images

            $sliderImages = json_decode($equipment->slider_images);



            foreach ($sliderImages as $sliderImage) {

                @unlink(public_path('assets/img/equipments/slider-images/') . $sliderImage);

            }



            // delete the equipment contents

            $equipmentContents = $equipment->content()->get();



            foreach ($equipmentContents as $equipmentContent) {

                $equipmentContent->delete();

            }



            // delete all the bookings of this equipment

            $bookings = $equipment->booking()->get();



            if (count($bookings) > 0) {

                foreach ($bookings as $booking) {

                    @unlink(public_path('assets/file/attachments/equipment/') . $booking->attachment);



                    @unlink(public_path('assets/file/invoices/equipment/') . $booking->invoice);



                    $booking->delete();

                }

            }



            // delete all the reviews of this equipment

            $equipmentReviews = $equipment->review()->get();



            if (count($equipmentReviews) > 0) {

                foreach ($equipmentReviews as $equipmentReview) {

                    $equipmentReview->delete();

                }

            }



            $equipment->delete();

        }



        Session::flash('success', 'Equipments deleted successfully!');



        return Response::json(['status' => 'success'], 200);

    }

}

