<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Exception;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class productController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except(['index', 'show']);
    }
    
    // public function carousel() {
    //     $products = product::select('id', 'title', 'short_title', 'image', 'desc', 'is_carousel', 'created_at', 'updated_at')
    //                     ->where('is_carousel', true)
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(3);

    //     return new productCollection($products);
    // }

    public function index(Request $request) {

        $asc = true;
        if (isset($request->sort[0]) && $request->sort[0] == '-') {
            $asc = false;
            $request->sort = substr($request->sort, 1);
        }
        $request->sort = $this->handleSort($request->sort) ? $request->sort : 'id';

        $products = Product::select('id', 'title', 'subtitle', 'slug','image', 'desc', 'created_at', 'updated_at')
                        ->when($request->search, function($q) use ($request) {
                            $q->where('title', 'like', '%'.$request->search.'%');
                            $q->orWhere('subtitle', 'like', '%'.$request->search.'%');
                        })
                        // ->when($request->categories, function($q) use ($request){
                        //     $q->whereHas('categories', function($q) use ($request){
                        //         $request->categories = explode(',', $request->categories);
                        //         $q->whereIn('product_categories.id', $request->categories);
                        //     });
                        // })
                        ->orderBy($request->sort, $asc ? 'asc' : 'desc')
                        ->paginate($request->page_limit ? $request->page_limit : 10);

        return new ProductCollection($products);
    }

    public function show($id){
        $product = Product::select('id', 'title', 'subtitle', 'slug','image', 'desc', 'created_at', 'updated_at')
                        ->where('id', $id)->first();
        if(!$product) return $this->returnCondition(false, 404, 'data tidak ditemukan');

        return new ProductResource($product);
    }

    public function store(ProductRequest $request) {
        
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
            // $categories = productCategory::select('id')->whereIn('id', $ids)->get()->toArray();
            // if (count($categories) <= 0) return $this->returnCondition(false, 404, 'category not found');

            // $categoryIds = [];
            // foreach ($categories as $item) {
            //     array_push($categoryIds, $item['id']);
            // }

            // if ($request->is_carousel == 1){
            //     $carousel = product::where('is_carousel', true)->get();
            //     if (count($carousel) >= 3) return $this->returnCondition(false, 404, 'maximal product carousel adalah 3');
            // }
            
            $create = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'slug' => $request->slug,
                'image' => $image,
                'desc' => $request->desc,
            ];

            $productId = Product::create($create)->id;

            // $product = Product::select('id')->where('id', $productId)->first();
            // $product->categories()->attach($categoryIds);

            Storage::putFileAs('public/images/product', $imageFile, $image);

            return $this->returnCondition(true, 200, 'Successfully created data');
        }catch(Exception $e){
            if(Storage::disk('local')->exists('public/images/product' . $image)){
                Storage::delete('public/images/product' . $image);
            }
            Log::error($e->getMessage());
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function update(Request $request, Product $product) {
            return response()->json($request);
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
                Storage::putFileAs('public/images/product', $imageFile, $image);

                $updateData['image'] = $image;
            }
        
            $product = Product::select('id', 'title')->where('id', $id)->first();
            if(!$product) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            // if ($request->is_carousel == 1){
            //     $carousel = product::where('is_carousel', true)->get();
            //     if (count($carousel) >= 3) return $this->returnCondition(false, 404, 'maximal product carousel adalah 3');
            // }

            // $ids = explode(',', $request->categories);
            // $categories = productCategory::select('id')->whereIn('id', $ids)->get()->toArray();
            // if (count($categories) <= 0) return $this->returnCondition(false, 404, 'category not found');

            // $categoryIds = [];
            // foreach ($categories as $item) {
            //     array_push($categoryIds, $item['id']);
            // }

            $oriImage = $product->image;

        try {

            $product->update($updateData);
            // $product->categories()->detach();
            // $product->categories()->attach($categoryIds);

            if($request->hasFile('image')){
                if($oriImage){
                    if(Storage::disk('local')->exists('public/images/product' . $oriImage)){
                        Storage::delete('public/images/product' . $oriImage);
                    }
                }
            }
            return $this->returnCondition(true, 200, 'Successfully updated data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            if($request->hasFile('image')){
                if(Storage::disk('local')->exists('public/images/product' . $image)){
                    Storage::delete('public/images/product' . $image);
                }
            }
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function destroy($id) {
        try {
            $product = Product::select('id', 'title', 'image')->where('id', $id)->first();
            if(!$product) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            $productImage = $product->image;

            // $product->categories()->detach();
            $product->delete();

            if($productImage){
                if(Storage::disk('local')->exists('public/images/product' . $productImage)){
                    Storage::delete('public/images/product' . $productImage);
                }
            }

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
