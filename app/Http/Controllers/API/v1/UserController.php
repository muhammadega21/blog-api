<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'User empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }


        return response()->json([
            'message' => 'List Users',
            'status' => Response::HTTP_OK,
            'data' => $users->map(function ($user) {
                return [
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'user_profile' => $user->user_profile,
                    'user_desc' => $user->user_desc,
                    'ig_url' => $user->ig_url,
                    'fb_url' => $user->fb_url,
                    'twitter_url' => $user->twitter_url,
                    'role' => [
                        'role_name' => $user->role->role_name
                    ]
                ];
            }),
        ], Response::HTTP_OK);
    }

    public function show($username)
    {
        $user = User::where('username', $username)->first();
        $posts = Post::where('user_id', $user->id)->get();

        if (!$user) {
            return response()->json([
                'message' => 'User empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' =>  [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'user_profile' => $user->user_profile,
                'user_desc' => $user->user_desc,
                'ig_url' => $user->ig_url,
                'fb_url' => $user->fb_url,
                'twitter_url' => $user->twitter_url,
                'role' => [
                    'role_name' => $user->role->role_name
                ],
                'posts' => $posts->map(function ($post) {
                    return [
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
                    ];
                }),
            ],

        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $rules = [
            'name' => 'required|min:4|max:50',
            'email' => 'required|email:rfc,dns',
            'user_profile' => 'mimes:png,jpg,jpeg|max:2048',
        ];

        if ($request->input('username') != $user->username) {
            $rules['username'] = 'required|min:4|max:10|unique:users,username';
        }

        $validator = Validator::make($request->all(), $rules, [
            'name.required' => 'Nama Tidak Boleh Kosong!',
            'name.min' => 'Nama Tidak Kurang Dari 4 Karakter!',
            'name.max' => 'Nama Tidak Lebih Dari 50 Karakter!',

            'username.required' => 'Username Tidak Boleh Kosong!',
            'username.min' => 'Username Tidak Kurang Dari 4 Karakter!',
            'username.max' => 'Username Tidak Lebih Dari 10 Karakter!',
            'username.unique' => 'Username Sudah Digunakan!',

            'email.required' => 'Email Tidak Boleh Kosong',
            'email.email' => 'Email Yang Anda Masukkan Salah!',

            'user_profile.mines' => 'Gambar Harus Berformat jpg, jpeg, dan png!',
            'user_profile.max' => 'Gambar Tidak Lebih dari 2mb!'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'user_profile' => $request->input('user_profile'),
                'user_desc' => $request->input('user_desc'),
                'ig_url' => $request->input('ig_url'),
                'fb_url' => $request->input('fb_url'),
                'twitter_url' => $request->input('twitter_url'),
                'role_id' => $user->role_id,
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
        $user = User::find($id);

        try {
            $user->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'User deleted'
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
