<?php

namespace Tests\Feature\Api;

use App\Http\Resources\AuthorSumaryResource;
use App\Models\Api\Author;
use App\Models\Api\Book;
use App\Models\User;
use Database\Factories\Api\CategoryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_index_authors(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $role = Role::create(['name'=>'admin','guard_name'=>'web']);
        $user->assignRole('admin');

        $author = Author::factory()->count(5)->create();

        $response = $this->get('/api/authors');
        
        $jsonStructure = [
            'message',
            'authors'=>[
                '*'=>[
                    'id','name','email','image_url'
                ]
            ]
        ];
        $response->assertStatus(200)->assertJsonStructure($jsonStructure);
    }

    public function test_store_athors(){
        Storage::fake('public');
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $author = Author::factory()->make();
        $response = $this->post('api/authors',[
            'name'=>$author->name,
            'email'=>$author->email,
            'image'=> UploadedFile::fake()->create('prueba.jpg')
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'author' => [
                'id',
                'name',
                'email',
                'image_url',
            ]
        ]);
        
        $this->assertNotEmpty(Storage::disk('public')->files('images'));
    }

    public function test_show_authors(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name'=>'admin','guard_name'=>'web']);
        $user->assignRole('admin');

        $author = Author::factory()->create();

        $response = $this->get('api/authors/'.$author->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'author'=>[
                'id',
                'name',
                'email',
                'image_url',
                'books'=>['*'=>['id','name','pages']]
            ]
        ]);
    }

    public function test_update_authors(){
        Storage::fake('public');
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $author = Author::factory()->create();
        $image = UploadedFile::fake()->image('actializada.jpg');
        $response = $this->put('/api/authors/'.$author->id,[
            'name'=>'actualizado',
            'email'=>'actualizado@gmail.com',
            'image'=> $image
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name'=>'actualizado',
            'email'=>'actualizado@gmail.com',
        ])
        ->assertExactJsonStructure([
            'message',
            'author' => [
                'id',
                'name',
                'email',
                'image_url', 
                'books'
            ]   
        ]);;

        $imageUrl = $response->json('author.image_url');
        $imagePath = str_replace('/storage/', '', $imageUrl);
        $absolutePath = Storage::disk('public')->path($imagePath);
        $this->assertFileExists($absolutePath); 
    }

    public function test_destroy_authors(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $author = Author::factory()->create();

        $response = $this->delete('/api/authors/'.$user->id);
        $response->assertStatus(200)->assertJsonStructure([
            'message'
        ]);
        $this->assertDatabaseMissing('authors',[
            'id'=>$author->id
        ]);
    }
}
