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
            'role_name' => 'required|max:15|unique:roles,role_name',
        ], [
            'role_name.required' => 'Nama Role Tidak Boleh Kosong!',
            'role_name.max' => 'Nama Role Tidak Lebih Dari 15 Karakter!',
            'role_name.unique' => 'Nama Role Ini Sudah Ada!',
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

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Role not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $rules = [];

        if ($request->input('role_name') != $role->role_name) {
            $rules['role_name'] = 'required|max:15|unique:roles,role_name';
        }

        $validator = Validator::make($request->all(), $rules, [
            'role_name.required' => 'Nama Role Tidak Boleh Kosong!',
            'role_name.max' => 'Nama Role Tidak Lebih Dari 15 Karakter!',
            'role_name.unique' => 'Nama Role Ini Sudah Ada!',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $role->update([
                'role_name' => $request->input('role_name'),
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
        $role = Role::find($id);

        try {
            $role->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Role deleted'
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
