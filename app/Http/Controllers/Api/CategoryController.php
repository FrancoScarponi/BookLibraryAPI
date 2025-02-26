<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Api\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::get();
        return response()->json([
            'message'=>'Listado de categorias.',
            'categories'=> CategoryResource::collection($categories)
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'description'=>'required|string',
        ]);

        $category = Category::create([
            'name'=>$request->name,
            'description'=>$request->description
        ]);

        return response()->json([
            'message'=>'Categoria creada.',
            'category'=> new CategoryResource($category)
        ],Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json([
            'message'=>'Datos de la categoria.',
            'category'=> new CategoryResource($category)
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'=>'required|string',
            'description'=>'required|string'
        ]);
        $category = Category::findOrFail($id);
        $category->update([
            'name'=>$request->name,
            'description'=>$request->description
        ]);
        return response()->json([
            'message'=>'Categoria actualizada.',
            'category'=> new CategoryResource($category)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json([
            'message'=>'Categoria eliminada'
        ],200);
    }
}
