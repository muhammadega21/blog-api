<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
}
