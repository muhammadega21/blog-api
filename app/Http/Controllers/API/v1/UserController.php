<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'user_desc' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'user_profile' => $request->input('user_profile'),
                'user_desc' => $request->input('user_desc'),
                'ig_url' => $request->input('ig_url'),
                'fb_url' => $request->input('fb_url'),
                'twitter_url' => $request->input('twitter_url'),
                'role_id' => $request->input('role_id'),
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
}
