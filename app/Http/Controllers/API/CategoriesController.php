<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $categories = Categories::all();       
        return ResponseHelper::success('List Data Categories', $categories, 200); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }

        $categories = Categories::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return ResponseHelper::success('Data Categories Berhasil Ditambahkan', $categories, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categories = Categories::find($id);       
        if (!$categories) {
            return ResponseHelper::error('Data not found', 404);
        } else {
            $categories->delete();
            return ResponseHelper::success('Categories '.$categories->name.' Berhasil Dihapus', null, 200);
        }
    }
}
