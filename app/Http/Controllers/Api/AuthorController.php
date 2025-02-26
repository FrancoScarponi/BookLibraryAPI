<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorSumaryResource;
use App\Models\Api\Author;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

use function PHPSTORM_META\map;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::get();

        return response()->json([
            'message'=>'Lista de autores.',
            'authors'=> AuthorSumaryResource::collection($authors),
        ],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:50',
            'email'=>'required|email|unique:users,email',
            'image'=>'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
        ]);

        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images','public');
        };

        $author = Author::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'image'=>$imagePath
        ]);

        return response()->json([
            'message'=>'Author creado.',
            'author'=> new AuthorResource($author),
        ],Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $author = Author::with('books.categories')->findOrFail($id);
        return response()->json([
            'message'=>'Author encontrado.',
            'author'=> new AuthorResource($author)
        ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'=>'required|string|max:50',
            'email'=>'required|email|unique:users,email,'. $id,
            'image'=>'nullable|image|mimes:png,jpg,jpeg,gif',
        ]);

        $author = Author::findOrFail($id);

        $imagePath = $author->image;
        
        if($request->hasFile('image')){
            if ($author->image && Storage::disk('public')->exists($author->image)) {
                Storage::disk('public')->delete($author->image);
            };
            $imagePath = $request->file('image')->store('images','public');
        };

    

        $author->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'image'=>$imagePath
        ]);

        $author->load('books');

        return response()->json([
            'message'=>"Autor actualizado.",
            'author'=> new AuthorResource($author),
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $author = Author::findOrFail($id);
        if(Storage::exists($author->image)){
            Storage::delete($author->image);
        }
        $author->delete();
        return response()->json([
            'message'=>'Autor eliminado',
        ],Response::HTTP_OK);
    }
}
