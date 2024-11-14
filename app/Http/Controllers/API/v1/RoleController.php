<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::get();

        if ($roles) {
            return response()->json([
                'message' => 'List Roles',
                'status' => Response::HTTP_OK,
                'data' => $roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'role_name' => $role->role_name
                    ];
                }),
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Role empty',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            Role::create([
                'role_name' => $request->input('role_name'),
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
