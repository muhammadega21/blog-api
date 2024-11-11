<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
                        'publish_date' => $post->created_at,
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
}
