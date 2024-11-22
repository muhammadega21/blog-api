<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::get();

        if (!$categories) {
            return response()->json([
                'message' => 'Post empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'List Category',
            'status' => Response::HTTP_OK,
            'data' => $categories->map(function ($category) {
                return [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon' => $category->icon,
                ];
            }),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30|unique:categories,name',
            'icon' => 'required|mimes:png,svg|max:2048',
        ], [
            'name.required' => 'Nama Kategori Tidak Boleh Kosong!',
            'name.max' => 'Nama Kategori Tidak Lebih 30 Karakter!',
            'name.unique' => 'Nama Kategori Sudah Ada!',

            'icon.required' => 'Icon Tidak Boleh Kosong!',
            'icon.mimes' => 'Icon Harus Berformat jpg dan svg!',
            'icon.max' => 'Icon Tidak Lebih dari 3mb!',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if ($request->file('icon')) {
            if ($request->oldIcon) {
                Storage::delete($request->oldIcon);
            }
            $icon = $request->file('icon')->store('categoryIcons');
        }

        try {
            Category::create([
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('name')),
                'icon' => $icon,
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

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $rules = [
            'icon' => 'required|mimes:png,svg|max:2048',
        ];

        if ($request->input('name') != $category->title) {
            $rules['name'] = 'required|max:30|unique:categories,name';
        }

        $validator = Validator::make($request->all(), $rules, [
            'name.required' => 'Nama Kategori Tidak Boleh Kosong!',
            'name.max' => 'Nama Kategori Tidak Lebih 30 Karakter!',
            'name.unique' => 'Nama Kategori Sudah Ada!',

            'icon.required' => 'Icon Tidak Boleh Kosong!',
            'icon.mimes' => 'Icon Harus Berformat jpg dan svg!',
            'icon.max' => 'Icon Tidak Lebih dari 3mb!',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if ($request->file('icon')) {
            if ($request->oldIcon) {
                Storage::delete($request->oldIcon);
            }
            $icon = $request->file('icon')->store('categoryIcons');
        }

        try {
            $category->update([
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('name')),
                'icon' => $icon,
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data updated'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error update data :' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'failed stored data to db'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        try {
            if ($category->icon) {
                Storage::delete($category->icon);
            }
            $category->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Category deleted'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error deleted data :' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'failed deleted data to db'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
