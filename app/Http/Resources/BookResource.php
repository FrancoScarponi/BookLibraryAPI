<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'pages'=>$this->pages,
            'author'=> new AuthorSumaryResource($this->whenLoaded('author')),
            'categories'=> CategoryResource::collection($this->whenLoaded('categories'))
        ];
    }
}
