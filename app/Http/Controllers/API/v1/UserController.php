<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();

        if ($users) {
            return response()->json([
                'message' => 'List Users',
                'status' => Response::HTTP_OK,
                'data' => $users->map(function ($user) {
                    return [
                        'name' => $user->name,
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
        } else {
            return response()->json([
                'message' => 'User empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
