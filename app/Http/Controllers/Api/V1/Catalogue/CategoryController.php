<?php

namespace App\Http\Controllers\Api\V1\Catalogue;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Catalogue\CategoryResource;
use App\Models\Catalogue\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            Category::query()->active()->orderBy('position')->get()
        );
    }
}
