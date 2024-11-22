<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query()->latest('created_at');

        $keyword = $request->input('title');
        if ($keyword) {
            $query->where('title', 'LIKE', '%' . $keyword . '%');
        }

        $posts = $query->paginate(10);

        if (!$posts) {
            return response()->json([
                'message' => 'Post empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'List Posts',
            'status' => Response::HTTP_OK,
            'data' => $posts->map(function ($post) {
                return [
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'body' => $post->body,
                    'image' => $post->image,
                    'publish_date' => $post->created_at->format('Y-m-d H:i:s'),
                    'user' => [
                        'name' => $post->user->name,
                        'user_profile' => $post->user->user_profile,
                    ],
                    'category' => [
                        'name' => $post->category->name,
                        'category_slug' => $post->category->slug,
                    ],
                ];
            }),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100|unique:posts,title',
            'body' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:3072',
            'user_id' => 'required',
            'category_id' => 'required',
        ], [
            'title.required' => 'Judul Tidak Boleh Kosong!',
            'title.max' => 'Judul Tidak Lebih 100 Karakter!',
            'title.unique' => 'Judul Ini Sudah Ada!',

            'body.required' => 'Isi Postingan Tidak Boleh Kosong!',

            'image.required' => 'Gambar Tidak Boleh Kosong!',
            'image.mimes' => 'Gambar Harus Berformat jpg, jpeg, dan png!',
            'image.max' => 'Gambar Tidak Lebih dari 3mb!',

            'category_id.required' => 'Kategori Tidak Boleh Kosong!',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if ($request->file('image')) {
            if ($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $image = $request->file('image')->store('postImages');
        }

        try {
            Post::create([
                'title' => $request->input('title'),
                'slug' => Str::slug($request->input('title')),
                'body' => $request->input('body'),
                'image' => $image,
                'user_id' => $request->input('user_id'),
                'category_id' => $request->input('category_id'),
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data stored to db'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error stored data :' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'failed stored data to db'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' =>  [
                'title' => $post->title,
                'slug' => $post->slug,
                'body' => $post->body,
                'image' => $post->image,
                'publish_date' => Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->toDateTimeString(),
                'user' => [
                    'name' => $post->user->name,
                    'user_profile' => $post->user->user_profile
                ],
                'category' => [
                    'name' => $post->category->category_name,
                    'category_slug' => $post->category->category_slug,
                ]
            ],

        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Post not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $rules = [
            'body' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:3072',
            'user_id' => 'required',
            'category_id' => 'required',
        ];

        if ($request->input('title') != $post->title) {
            $rules['title'] = 'required|max:100|unique:posts,title';
        }

        $validator = Validator::make($request->all(), $rules, [
            'title.required' => 'Judul Tidak Boleh Kosong!',
            'title.max' => 'Judul Tidak Lebih 100 Karakter!',
            'title.unique' => 'Judul Ini Sudah Ada!',

            'body.required' => 'Isi Postingan Tidak Boleh Kosong!',

            'image.required' => 'Gambar Tidak Boleh Kosong!',
            'image.mimes' => 'Gambar Harus Berformat jpg, jpeg, dan png!',
            'image.max' => 'Gambar Tidak Lebih dari 3mb!',

            'category_id.required' => 'Kategori Tidak Boleh Kosong!',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if ($request->file('image')) {
            if ($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $image = $request->file('image')->store('postImages');
        }


        try {
            $post->update([
                'title' => $request->input('title'),
                'slug' => Str::slug($request->input('title')),
                'body' => $request->input('body'),
                'image' => $image,
                'user_id' => $request->input('user_id'),
                'category_id' => $request->input('category_id'),
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Post Berhasil Diupdate'
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
        $post = Post::find($id);

        try {
            if ($post->image) {
                Storage::delete($post->image);
            }
            $post->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Postingan Berhasil Dihapus'
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
