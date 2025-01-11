<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Exception;
use App\Models\Service;
// use App\Models\ServiceCategory;
use App\Http\Requests\ServiceRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Http\Resources\Service\ServiceCollection;
use App\Http\Resources\Service\ServiceResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except(['index', 'show']);
    }
    
    // public function carousel() {
    //     $services = Service::select('id', 'title', 'short_title', 'image', 'desc', 'is_carousel', 'created_at', 'updated_at')
    //                     ->where('is_carousel', true)
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(3);

    //     return new ServiceCollection($services);
    // }

    public function index(Request $request) {

        $asc = true;
        if (isset($request->sort[0]) && $request->sort[0] == '-') {
            $asc = false;
            $request->sort = substr($request->sort, 1);
        }
        $request->sort = $this->handleSort($request->sort) ? $request->sort : 'id';

        $services = Service::select('id', 'title', 'subtitle', 'slug','image', 'desc', 'created_at', 'updated_at')
                        ->when($request->search, function($q) use ($request) {
                            $q->where('title', 'like', '%'.$request->search.'%');
                            $q->orWhere('subtitle', 'like', '%'.$request->search.'%');
                        })
                        // ->when($request->categories, function($q) use ($request){
                        //     $q->whereHas('categories', function($q) use ($request){
                        //         $request->categories = explode(',', $request->categories);
                        //         $q->whereIn('Service_categories.id', $request->categories);
                        //     });
                        // })
                        ->orderBy($request->sort, $asc ? 'asc' : 'desc')
                        ->paginate(10);

        return new ServiceCollection($services);
    }

    public function show($id){
        $service = Service::select('id', 'title', 'subtitle', 'slug','image', 'desc', 'created_at', 'updated_at')
                        ->where('id', $id)->first();
        if(!$service) return $this->returnCondition(false, 404, 'data tidak ditemukan');

        return new ServiceResource($service);
    }

    public function store(ServiceRequest $request) {
        
        $rules = [
            'image' => 'required|mimes:jpg,jpeg,png|max:5048',
        ];

        Validator::make($request->all(), $rules, $messages = 
        [
            'image.required' => 'Gambar harus diisi',
            'image.mimes'    => 'Gambar harus berupa jpg, png atau jpeg',
            'image.max'      => 'Maximum gambar adalah 5 MB',
        ])->validate();
        
        try {

            $imageFile = $request->file('image');
            $image     = time() . '-' . $imageFile->getClientOriginalName();

            // $ids = explode(',', $request->categories);
            // $categories = ServiceCategory::select('id')->whereIn('id', $ids)->get()->toArray();
            // if (count($categories) <= 0) return $this->returnCondition(false, 404, 'category not found');

            // $categoryIds = [];
            // foreach ($categories as $item) {
            //     array_push($categoryIds, $item['id']);
            // }

            // if ($request->is_carousel == 1){
            //     $carousel = Service::where('is_carousel', true)->get();
            //     if (count($carousel) >= 3) return $this->returnCondition(false, 404, 'maximal Service carousel adalah 3');
            // }
            
            $create = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'slug' => $request->slug,
                'image' => $image,
                'desc' => $request->desc,
            ];

            $serviceId = Service::create($create)->id;

            // $service = Service::select('id')->where('id', $serviceId)->first();
            // $service->categories()->attach($categoryIds);

            Storage::putFileAs('public/images/service', $imageFile, $image);

            return $this->returnCondition(true, 200, 'Successfully created data');
        }catch(Exception $e){
            if(Storage::disk('local')->exists('public/images/service' . $image)){
                Storage::delete('public/images/service' . $image);
            }
            Log::error($e->getMessage());
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function update(ServiceRequest $request, $id) {

            $updateData = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'slug' => $request->slug,
                'desc' => $request->desc,
            ];

            if($request->hasFile('image')){

                $rules = [
                    'image' => 'mimes:jpg,png,jpeg|max:5048',
                ];

                Validator::make($request->all(), $rules, $messages = 
                [
                    'image.mimes' => 'gambar harus berupa jpg, png atau jpeg',
                    'image.max'   => 'maximum gambar adalah 5 MB',
                ])->validate();

                $imageFile      = $request->file('image');
                $image          = time() . '-' . $imageFile->getClientOriginalName();
                Storage::putFileAs('public/images/service', $imageFile, $image);

                $updateData['image'] = $image;
            }
        
            $service = Service::select('id', 'title')->where('id', $id)->first();
            if(!$service) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            // if ($request->is_carousel == 1){
            //     $carousel = Service::where('is_carousel', true)->get();
            //     if (count($carousel) >= 3) return $this->returnCondition(false, 404, 'maximal Service carousel adalah 3');
            // }

            // $ids = explode(',', $request->categories);
            // $categories = ServiceCategory::select('id')->whereIn('id', $ids)->get()->toArray();
            // if (count($categories) <= 0) return $this->returnCondition(false, 404, 'category not found');

            // $categoryIds = [];
            // foreach ($categories as $item) {
            //     array_push($categoryIds, $item['id']);
            // }

            $oriImage = $service->image;

        try {

            $service->update($updateData);
            // $service->categories()->detach();
            // $service->categories()->attach($categoryIds);

            if($request->hasFile('image')){
                if($oriImage){
                    if(Storage::disk('local')->exists('public/images/service' . $oriImage)){
                        Storage::delete('public/images/service' . $oriImage);
                    }
                }
            }
            return $this->returnCondition(true, 200, 'Successfully updated data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            if($request->hasFile('image')){
                if(Storage::disk('local')->exists('public/images/service' . $image)){
                    Storage::delete('public/images/service' . $image);
                }
            }
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function destroy($id) {
        try {
            $service = Service::select('id', 'title', 'image')->where('id', $id)->first();
            if(!$service) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            $serviceImage = $service->image;

            // $service->categories()->detach();
            $service->delete();

            if($serviceImage){
                if(Storage::disk('local')->exists('public/images/service' . $serviceImage)){
                    Storage::delete('public/images/service' . $serviceImage);
                }
            }

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
