<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthCotroller extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:50',
            'username' => 'required|min:4|max:10|unique:users,username',
            'password' => 'required|min:4',
            'email' => 'required|email:rfc,dns'
        ], [
            'name.required' => 'Nama Tidak Boleh Kosong!',
            'name.min' => 'Nama Tidak Kurang Dari 4 Karakter!',
            'name.max' => 'Nama Tidak Lebih Dari 50 Karakter!',

            'username.required' => 'Username Tidak Boleh Kosong!',
            'username.min' => 'Username Tidak Kurang Dari 4 Karakter!',
            'username.max' => 'Username Tidak Lebih Dari 10 Karakter!',
            'username.unique' => 'Username Sudah Digunakan!',

            'password.required' => 'Password Tidak Boleh Kosong',
            'password.min' => 'Password Tidak Kurang Dari 4 Karakter',

            'email.required' => 'Email Tidak Boleh Kosong',
            'email.email' => 'Email Yang Anda Masukkan Salah!',

            'user_profile.mines' => 'Gambar Harus Berformat jpg, jpeg, dan png!',
            'user_profile.max' => 'Gambar Tidak Lebih dari 2mb!'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'user_profile' => 'img/user.png',
                'role_id' => 3
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'User register successfully.',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:4',
        ], [
            'email.required' => 'Email Tidak Boleh Kosong',
            'email.email' => 'Email Yang Anda Masukkan Salah!',

            'password.required' => 'Password Tidak Boleh Kosong',
            'password.min' => 'Password Tidak Kurang Dari 4 Karakter',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if (!Auth::attempt($credentials)) {
            if (!$user || !Hash::check($request['password'], $user->password)) {
                return response()->json([
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Email atau password salah!'
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

        $token = $user->createToken('login_token')->plainTextToken;
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'User login successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], Response::HTTP_OK);
    }
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Logout'
        ], Response::HTTP_OK);
    }
}
