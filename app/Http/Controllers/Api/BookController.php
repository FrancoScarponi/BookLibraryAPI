<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Api\Book;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with('author:id,name,email,image')->with('categories:id,name,description')->get();
        return response()->json([
            'message'=>"Lista de libros.",
            'books'=> BookResource::collection($books)
        ],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'pages'=>'required|integer',
            'author_id'=>'required|integer|exists:authors,id',
            'categories_id'=>'array',
            'categories_id.*'=>'nullable|exists:categories,id'
        ]);

        $book = Book::create([
            'name'=>$request->name,
            'pages'=>$request->pages,
            'author_id'=>$request->author_id,
        ]);
        if($request->has('categories_id')){
            $book->categories()->attach($request->categories_id);
        }
        $book->load(['categories','author']);
        return response()->json([
            'message'=>'Libro creado.',
            'book'=> new BookResource($book)
        ],Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::with(['author','categories'])->findOrFail($id);
        
        return response()->json([
            'message'=>'Datos de la categoria.',
            'book'=> new BookResource($book)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'=>'required|string',
            'pages'=>'required|integer',
            'author_id'=>'required|integer|exists:authors,id',
            'categories_id'=>'array',
            'categories_id.*'=>'nullable|exists:categories,id'
        ]);
        $book = Book::findOrFail($id);
        $book->update([
            'name'=>$request->name,
            'pages'=>$request->pages,
            'author_id'=>$request->author_id
        ]);
        if($request->has('categories_id')){
            $book->categories()->sync($request->categories_id);
        }
        $book->load(['categories','author']);
        return response()->json([
            'message'=>'Libro actualizado.',
            'book'=> new BookResource($book)
        ], Response::HTTP_OK);
    }   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return response()->json([
            'message'=>'Libro eliminado.'
        ],Response::HTTP_OK);
    }
}
