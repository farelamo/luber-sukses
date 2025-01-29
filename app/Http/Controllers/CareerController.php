<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Exception;
use App\Models\Career;
// use App\Models\CareerCategory;
use App\Http\Requests\CareerRequest;
// use App\Http\Requests\CareerUpdateRequest;
use App\Http\Resources\Career\CareerCollection;
use App\Http\Resources\Career\CareerResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CareerController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except(['index', 'show']);
    }
    
    // public function carousel() {
    //     $careers = Career::select('id', 'title', 'short_title', 'image', 'desc', 'is_carousel', 'created_at', 'updated_at')
    //                     ->where('is_carousel', true)
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(3);

    //     return new CareerCollection($careers);
    // }

    public function index(Request $request) {

        $asc = true;
        if (isset($request->sort[0]) && $request->sort[0] == '-') {
            $asc = false;
            $request->sort = substr($request->sort, 1);
        }
        $request->sort = $this->handleSort($request->sort, null) ? $request->sort : 'id';

        
        $careers = Career::select('id', 'title', 'job_open', 'job_closed', 'desc', 'created_at', 'updated_at')
                        ->when($request->search, function($q) use ($request) {
                            $q->where('title', 'like', '%'.$request->search.'%');
                            $q->orWhere('subtitle', 'like', '%'.$request->search.'%');
                        })
                        // ->when($request->categories, function($q) use ($request){
                        //     $q->whereHas('categories', function($q) use ($request){
                        //         $request->categories = explode(',', $request->categories);
                        //         $q->whereIn('Career_categories.id', $request->categories);
                        //     });
                        // })
                        ->orderBy($request->sort, $asc ? 'asc' : 'desc')
                        ->paginate(10);

        return new CareerCollection($careers);
    }

    public function show($id){
        $career = Career::select('id', 'title', 'job_open', 'job_closed', 'desc', 'created_at', 'updated_at')
                        ->where('id', $id)->first();
        if(!$career) return $this->returnCondition(false, 404, 'data tidak ditemukan');

        return new CareerResource($career);
    }

    public function store(CareerRequest $request) {
        
        // $rules = [
        //     'image' => 'required|mimes:jpg,jpeg,png|max:5048',
        // ];

        // Validator::make($request->all(), $rules, $messages = 
        // [
        //     'image.required' => 'Gambar harus diisi',
        //     'image.mimes'    => 'Gambar harus berupa jpg, png atau jpeg',
        //     'image.max'      => 'Maximum gambar adalah 5 MB',
        // ])->validate();
        
        try {

            // $imageFile = $request->file('image');
            // $image     = time() . '-' . $imageFile->getClientOriginalName();

            // $ids = explode(',', $request->categories);
            // $categories = CareerCategory::select('id')->whereIn('id', $ids)->get()->toArray();
            // if (count($categories) <= 0) return $this->returnCondition(false, 404, 'category not found');

            // $categoryIds = [];
            // foreach ($categories as $item) {
            //     array_push($categoryIds, $item['id']);
            // }

            // if ($request->is_carousel == 1){
            //     $carousel = Career::where('is_carousel', true)->get();
            //     if (count($carousel) >= 3) return $this->returnCondition(false, 404, 'maximal Career carousel adalah 3');
            // }
            
            $create = [
                'title' => $request->title,
                'job_open' => $request->job_open,
                'job_closed' => $request->job_closed,
                'desc' => $request->desc,
            ];

            $careerId = Career::create($create)->id;

            // $career = Career::select('id')->where('id', $careerId)->first();
            // $career->categories()->attach($categoryIds);

            // Storage::putFileAs('public/images/career', $imageFile, $image);

            return $this->returnCondition(true, 200, 'Successfully created data');
        }catch(Exception $e){
            // if(Storage::disk('local')->exists('public/images/career' . $image)){
            //     Storage::delete('public/images/career' . $image);
            // }
            Log::error($e->getMessage());
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function update(CareerRequest $request, $id) {

            $updateData = [
                'title' => $request->title,
                'job_open' => $request->job_open,
                'job_closed' => $request->job_closed,
                'desc' => $request->desc,
            ];

            // if($request->hasFile('image')){

            //     $rules = [
            //         'image' => 'mimes:jpg,png,jpeg|max:5048',
            //     ];

            //     Validator::make($request->all(), $rules, $messages = 
            //     [
            //         'image.mimes' => 'gambar harus berupa jpg, png atau jpeg',
            //         'image.max'   => 'maximum gambar adalah 5 MB',
            //     ])->validate();

            //     $imageFile      = $request->file('image');
            //     $image          = time() . '-' . $imageFile->getClientOriginalName();
            //     Storage::putFileAs('public/images/career', $imageFile, $image);

            //     $updateData['image'] = $image;
            // }
        
            $career = Career::select('id', 'title')->where('id', $id)->first();
            if(!$career) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            // if ($request->is_carousel == 1){
            //     $carousel = Career::where('is_carousel', true)->get();
            //     if (count($carousel) >= 3) return $this->returnCondition(false, 404, 'maximal Career carousel adalah 3');
            // }

            // $ids = explode(',', $request->categories);
            // $categories = CareerCategory::select('id')->whereIn('id', $ids)->get()->toArray();
            // if (count($categories) <= 0) return $this->returnCondition(false, 404, 'category not found');

            // $categoryIds = [];
            // foreach ($categories as $item) {
            //     array_push($categoryIds, $item['id']);
            // }

            // $oriImage = $career->image;

        try {

            $career->update($updateData);
            // $career->categories()->detach();
            // $career->categories()->attach($categoryIds);

            // if($request->hasFile('image')){
            //     if($oriImage){
            //         if(Storage::disk('local')->exists('public/images/career' . $oriImage)){
            //             Storage::delete('public/images/career' . $oriImage);
            //         }
            //     }
            // }
            return $this->returnCondition(true, 200, 'Successfully updated data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            // if($request->hasFile('image')){
            //     if(Storage::disk('local')->exists('public/images/career' . $image)){
            //         Storage::delete('public/images/career' . $image);
            //     }
            // }
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function destroy($id) {
        try {
            $career = Career::select('id', 'title', 'image')->where('id', $id)->first();
            if(!$career) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            // $careerImage = $career->image;

            // $career->categories()->detach();
            $career->delete();

            // if($careerImage){
            //     if(Storage::disk('local')->exists('public/images/career' . $careerImage)){
            //         Storage::delete('public/images/career' . $careerImage);
            //     }
            // }

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
