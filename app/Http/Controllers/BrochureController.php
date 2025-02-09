<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Exception;
use App\Models\Brochure;
use App\Models\BrochureCategory;
use App\Http\Requests\BrochureRequest;
use App\Http\Requests\BrochureUpdateRequest;
use App\Http\Resources\Brochure\BrochureCollection;
use App\Http\Resources\Brochure\BrochureResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BrochureController extends Controller
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
        $request->sort = $this->handleSort($request->sort, ['id', 'title', 'is_choosen', 'created_at', 'updated_at']) ? $request->sort : 'id';

        $brocuhers = Brochure::select('id', 'title', 'is_choosen', 'file', 'created_at', 'updated_at')
                        ->where(function($q) use ($request) {
                            if ($request->search) $q->where('title', 'like', '%'.$request->search.'%');
                            if ($request->is_choosen !== null) $q->where('is_choosen', $request->is_choosen);
                        })
                        ->orderBy($request->sort, $asc ? 'asc' : 'desc')
                        ->paginate($request->page_limit ? $request->page_limit : 10);

        return new BrochureCollection($brocuhers);
    }

    public function show($id){
        $brochure = Brochure::select('id', 'title', 'file', 'is_choosen', 'created_at', 'updated_at')
                        ->where('id', $id)->first();
        if(!$brochure) return $this->returnCondition(false, 404, 'data tidak ditemukan');

        return new BrochureResource($brochure);
    }

    public function store(BrochureRequest $request) {
        
        $rules = [
            'file' => 'required|mimetypes:application/pdf|max:5048',
        ];

        Validator::make($request->all(), $rules, $messages = 
        [
            'file.required' => 'File harus diisi',
            'file.mimetypes:application/pdf' => 'File harus berupa pdf',
            'file.max'      => 'Maximum file adalah 5 MB',
        ])->validate();
        
        try {

            $file    = $request->file('file');
            $pdfFile = time() . '-' . $file->getClientOriginalName();
            
            $create = [
                'title' => $request->title,
                'is_choosen' => $request->is_choosen,
                'file' => $pdfFile,
            ];

            $brochureId = Brochure::create($create)->id;

            Storage::putFileAs('public/pdf/brochure', $file, $pdfFile);

            return $this->returnCondition(true, 200, 'Successfully created data');
        }catch(Exception $e){
            if(Storage::disk('local')->exists('public/pdf/brochure' . $pdfFile)){
                Storage::delete('public/pdf/brochure' . $pdfFile);
            }
            Log::error($e->getMessage());
            return $this->returnCondition(false,500, 'Internal server error');
        }
    }

    public function update(Request $request, Brochure $brochure) {

        $updateData = [
            'title' => $request->title,
            'is_choosen' => $request->is_choosen,
        ];

        if($request->hasFile('file')){

            $rules = [
                'file' => 'mimes:pdf|max:5048',
            ];

            Validator::make($request->all(), $rules, $messages = 
            [
                'file.mimes' => 'file harus berupa pdf',
                'file.max'   => 'maximum file adalah 5 MB',
            ])->validate();

            $file      = $request->file('file');
            $pdfFile          = time() . '-' . $file->getClientOriginalName();
            Storage::putFileAs('public/pdf/brochure', $file, $pdfFile);

            $updateData['file'] = $pdfFile;
        }
    
        $brochure = Brochure::select('id', 'title')->where('id', $brochure->id)->first();
        if(!$brochure) return $this->returnCondition(false, 404, 'data tidak ditemukan');

        $oriImage = $brochure->file;

        try {

            $brochure->update($updateData);
        
            if($request->hasFile('file')){
                if($oriImage){
                    if(Storage::disk('local')->exists('public/pdf/brochure' . $oriImage)){
                        Storage::delete('public/pdf/brochure' . $oriImage);
                    }
                }
            }
            return $this->returnCondition(true, 200, 'Successfully updated data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            if($request->hasFile('file')){
                if(Storage::disk('local')->exists('public/pdf/brochure' . $pdfFile)){
                    Storage::delete('public/pdf/brochure' . $pdfFile);
                }
            }
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function destroy($id) {
        try {
            $brochure = Brochure::select('id', 'title', 'file')->where('id', $id)->first();
            if(!$brochure) return $this->returnCondition(false, 404, 'data tidak ditemukan');

            $brochure->delete();

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}
