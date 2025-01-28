<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Exception;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Http\Requests\BrandRequest;
use App\Http\Requests\BrandUpdateRequest;
use App\Http\Resources\Brand\BrandCollection;
use App\Http\Resources\Brand\BrandResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function index(Request $request) {

        $asc = true;
        if (isset($request->sort[0]) && $request->sort[0] == '-') {
            $asc = false;
            $request->sort = substr($request->sort, 1);
        }
        $request->sort = $this->handleSort($request->sort, ['id', 'title', 'created_at', 'updated_at', 'is_show']) ? $request->sort : 'id';

        $brands = Brand::select('id', 'title', 'image', 'is_show', 'created_at', 'updated_at')
                        ->when($request->search, function($q) use ($request) {
                            $q->where('title', 'like', '%'.$request->search.'%');
                            $q->orWhere('subtitle', 'like', '%'.$request->search.'%');
                        })
                        ->when($request->is_show, function($q) use ($request){
                            $q->where('is_show', $request->is_show);
                        })
                        ->orderBy($request->sort, $asc ? 'asc' : 'desc')
                        ->paginate($request->page_limit ? $request->page_limit : 10);

        return new BrandCollection($brands);
    }

    public function show($id){
        $brand = Brand::select('id', 'title', 'image', 'is_show', 'created_at', 'updated_at')
                        ->where('id', $id)->first();
        if(!$brand) return $this->returnCondition(false, 404, 'data tidak ditemukan');

        return new BrandResource($brand);
    }

    public function store(BrandRequest $request) {
        
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
            
            $create = [
                'title' => $request->title,
                'image' => $image,
                'is_show' => $request->is_show,
            ];

            $brandId = Brand::create($create)->id;

            Storage::putFileAs('public/images/brand', $imageFile, $image);

            return $this->returnCondition(true, 200, 'Successfully created data');
        }catch(Exception $e){
            if(Storage::disk('local')->exists('public/images/brand' . $image)){
                Storage::delete('public/images/brand' . $image);
            }
            Log::error($e->getMessage());
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function update(Request $request, Brand $brand) {

        $updateData = [
            'title' => $request->title,
            'is_show' => $request->is_show,
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
            Storage::putFileAs('public/images/brand', $imageFile, $image);

            $updateData['image'] = $image;
        }
    
        $brand = Brand::select('id', 'title')->where('id', $id)->first();
        if(!$brand) return $this->returnCondition(false, 404, 'data tidak ditemukan');

        $oriImage = $brand->image;

        try {

            $brand->update($updateData);

            if($request->hasFile('image')){
                if($oriImage){
                    if(Storage::disk('local')->exists('public/images/brand' . $oriImage)){
                        Storage::delete('public/images/brand' . $oriImage);
                    }
                }
            }
            return $this->returnCondition(true, 200, 'Successfully updated data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            if($request->hasFile('image')){
                if(Storage::disk('local')->exists('public/images/brand' . $image)){
                    Storage::delete('public/images/brand' . $image);
                }
            }
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function destroy($id) {
        try {
            $brand = Brand::select('id', 'title', 'image')->where('id', $id)->first();
            if(!$brand) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            // $brandImage = $brand->image;

            $brand->delete();

            // if($brandImage){
            //     if(Storage::disk('local')->exists('public/images/brand' . $brandImage)){
            //         Storage::delete('public/images/brand' . $brandImage);
            //     }
            // }

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
