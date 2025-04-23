<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu = Menu::with('categories')->get();
        return ResponseHelper::success('List Data Menu', $menu, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categories_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:menus',
            'description' => 'string',
            'price' => 'required|numeric',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }

        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('menus', 'public'); 
            $imageUrl = "/storage/" . $imagePath; // Simpan URL untuk akses gambar
        } else {
            return ResponseHelper::error('Gambar tidak ditemukan', 400);
        }

        $menu = Menu::create([
            'categories_id' => $request->categories_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image_url' => url($imageUrl),
        ]);
        
        return ResponseHelper::success('Berhasil Menambahkan Data Menu', $menu, 200);
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
        //
    }
}
