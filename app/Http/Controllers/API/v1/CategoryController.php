<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::get();

        if ($categories) {
            return response()->json([
                'message' => 'List Category',
                'status' => Response::HTTP_OK,
                'data' => $categories->map(function ($category) {
                    return [
                        'category_name' => $category->category_name,
                        'category_slug' => $category->category_slug,
                        'category_icon' => $category->category_icon,
                    ];
                }),
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Category empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
