<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'category_icon' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            Category::create([
                'category_name' => $request->input('category_name'),
                'category_slug' => Str::slug($request->input('category_name')),
                'category_icon' => $request->input('category_icon'),
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data stored to db'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error  storting data :' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'failed stored data to db' .
                    $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
