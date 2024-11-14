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


class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest('publish_date')->get();

        if ($posts) {
            return response()->json([
                'message' => 'List Posts',
                'status' => Response::HTTP_OK,
                'data' => $posts->map(function ($post) {
                    return [
                        'post_title' => $post->post_title,
                        'post_slug' => $post->post_slug,
                        'post_content' => $post->post_content,
                        'post_image' => $post->post_image,
                        'publish_date' => Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->toDateTimeString(),
                        'user' => [
                            'name' => $post->user->name,
                            'user_profile' => $post->user->user_profile
                        ],
                        'category' => [
                            'name' => $post->category->category_name,
                            'category_slug' => $post->category->category_slug,
                        ]
                    ];
                }),
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Post empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_title' => 'required',
            'post_content' => 'required',
            'post_image' => 'required',
            'user_id' => 'required',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            Post::create([
                'post_title' => $request->input('post_title'),
                'post_slug' => Str::slug($request->input('post_title')),
                'post_content' => $request->input('post_content'),
                'post_image' => $request->input('post_image'),
                'user_id' => $request->input('user_id'),
                'category_id' => $request->input('category_id'),
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data stored to db'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error  storting data :' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'failed stored data to db'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($slug)
    {
        $post = Post::where('post_slug', $slug)->first();

        if ($post) {
            return response()->json([
                'message' => 'Post by ' . $post->user->name,
                'status' => Response::HTTP_OK,
                'data' =>  [
                    'post_title' => $post->post_title,
                    'post_slug' => $post->post_slug,
                    'post_content' => $post->post_content,
                    'post_image' => $post->post_image,
                    'publish_date' => Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->toDateTimeString(),
                    'user' => [
                        'name' => $post->user->name,
                        'user_profile' => $post->user->user_profile
                    ],
                    'category' => [
                        'name' => $post->category->category_name,
                        'category_slug' => $post->category->category_slug,
                    ]
                ]
            ]);
        } else {
            return response()->json([
                'message' => 'Post empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
