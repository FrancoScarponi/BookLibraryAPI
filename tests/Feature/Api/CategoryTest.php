<?php

namespace Tests\Feature\Api;

use App\Models\Api\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    
    public function test_index_categories(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name'=>'admin','guard_name'=>'web']);
        $user->assignRole('admin');

        $categories = Category::factory()->count(2)->create();

        $response = $this->get('api/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'categories'=>[
                '*'=>[
                    'id',
                    'name',
                    'description'
                ]
            ]
        ]);
        $response->assertJsonFragment([
            'id'=>$categories[0]->id,
            'name'=>$categories[0]->name,
            'description'=>$categories[0]->description
        ]);
    }

    public function test_store_categories(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $category = Category::factory()->create();
        $response = $this->post('api/categories',[
            'name'=>$category->name,
            'description'=>$category->description
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'category'=>['id','name','description']
        ]);
        $this->assertDatabaseHas('categories',[
            'name'=>$category->name,
            'description'=>$category->description
        ]);
    }

    public function test_show_categories(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name'=>'admin','guard_name'=>'web']);
        $user->assignRole('admin');

        $categories= Category::factory()->count(5)->create();

        $response = $this->get('api/categories/'.$categories[3]->id);
        $response->assertStatus(200);
        $response->assertExactJsonStructure([
            'message',
            'category'=>['id','name','description']
        ]);
        $response->assertJsonFragment([
            'category'=>[
                'id'=>$categories[3]->id,
                'name'=>$categories[3]->name,
                'description'=>$categories[3]->description
            ]
        ]);
    }
    
    public function test_update_categories(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();

        $response = $this->put('api/categories/'.$category->id,[
            'name'=>'actualizado',
            'description'=>'Esta es una categoria actualizada'
        ]);

        $response->assertStatus(200);
        $response->assertExactJsonStructure([
            'message',
            'category'=>['id','name','description']
        ]);
        $response->assertJsonFragment([
            'category'=>[
                'id'=>$category->id,
                'name'=>'actualizado',
                'description'=>'Esta es una categoria actualizada'
            ]
        ]);
        $this->assertDatabaseHas('categories',[
            'name'=>'actualizado',
            'description'=>'Esta es una categoria actualizada'
        ]);
    }

    public function test_destroy_categories(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $category = Category::factory()->create();
        $response = $this->delete('api/categories/'.$category->id);
        $response->assertStatus(200);
        $response->assertExactJsonStructure([
            'message'
        ]);
        $this->assertDatabaseMissing('categories',[
            'id'=>$category->id
        ]);
    }
}
