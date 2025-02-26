<?php

namespace Tests\Feature\Api;

use App\Models\Api\Author;
use App\Models\Api\Book;
use App\Models\Api\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_index_books(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name'=>'admin','guard_name'=>'web']);
        $user->assignRole('admin');

        $response = $this->get('/api/books');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'books'=>[
                '*'=>[
                    'id',
                    'name',
                    'pages',
                    'categories',
                ]
            ]
        ]);
    }

    public function test_store_books(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->make();
        $author = Author::factory()->create();
        $response = $this->post('api/books',[
            'name'=>$book->name,
            'pages'=>$book->pages,
            'author_id'=>$author->id
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'book'=>[
                'id',
                'name',
                'pages',
                'author'
            ]
        ]);
        $response->assertJsonFragment([
            'author'=>[
                'id'=>$author->id,
                'name'=>$author->name,
                'email'=>$author->email,
                'image_url'=> Storage::url($author->image)
            ]
        ]);
        $this->assertDatabaseHas('books',[
            'name'=>$book->name,
            'pages'=>$book->pages
        ]);
    }

    public function test_show_books(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name'=>'admin','guard_name'=>'web']);
        $user->assignRole('admin');

        $book = Book::factory()->create();
        $response = $this->get('api/books/'.$book->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'book'=>[
                'id',
                'name',
                'pages',
                'author',
                'categories'
            ]
        ]);
    }

    public function test_update_books(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $author = Author::factory()->create();
        $book=Book::factory()->create();
        $categories = Category::factory()->count(4)->create();
        $book->categories()->attach([$categories[0]->id,$categories[1]->id]);
        $response = $this->put('api/books/'.$book->id,[
            'name'=>'Actualizado',
            'pages'=>111,
            'author_id'=>$author->id,
            'categories_id'=>[$categories[2]->id,$categories[3]->id]
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'book'=>[
                'id',
                'name',
                'pages',
                'author'=>['id','name','email','image_url'],
                'categories'=>['*'=>['id','name','description']]
            ]
        ]);
        $this->assertDatabaseHas('book_category', [
            'book_id' => $book->id,
            'category_id' => $categories[2]->id,
        ]);
    
        $this->assertDatabaseHas('book_category', [
            'book_id' => $book->id,
            'category_id' => $categories[3]->id,
        ]);

        $response->assertJsonFragment([
            'id'=>$categories[2]->id,
            'name'=>$categories[2]->name
        ]);
        $response->assertJsonMissing([
            'categories'=>[
                'id'=>$categories[0]->id,
                'name'=>$categories[0]->name
            ]
        ]);
    }

    public function test_destroy_books(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create();
        $response = $this->delete('api/books/'.$book->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('books',[
            'id'=>$book->id
        ]);
    }
}
