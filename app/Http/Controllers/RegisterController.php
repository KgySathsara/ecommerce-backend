<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Fetch the role ID for the 'user' role
            $role = Role::where('role_name', 'user')->first();
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 500);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $role->id, // Set user_type to the role ID
            ]);

            return response()->json(['message' => 'User registered successfully'], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Registration error: ' . $e->getMessage());
            // Return the error message in the response
            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }
}
