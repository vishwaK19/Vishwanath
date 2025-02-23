<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|lowercase|unique:users',
                'password' => 'required|confirmed|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$*])[A-Za-z\d@#$*]{8,}$/',
            ],[
                'email.unique' => 'This email address is already registered.',
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@, #, $, *).'
            ]);
    
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
    
            return response()->json([
                'user' => $user,
                'message' => 'User Registered Successfully',
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function login(Request $request) {
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);
    
            if(!Auth::attempt(['email' => $validated['email'],'password' => $validated['password']])) {
                return response()->json(['message' => 'Invalid Credentials'], 401);
            }
    
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login Successful',
                'token' => $token,
                'user' => ['name'=>$user->name, 'id'=>$user->uuid]
            ]);
        } catch(ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function sendResetLink(Request $request) {
        try{
            $request->validate(['email' => 'required|lowercase|email|exists:users,email']);
            $status = Password::sendResetLink(
                $request->only('email')
            ); 

            return $status === Password::RESET_LINK_SENT ? response()->json(['message' => 'Reset Link Sent to email']) : response()->json(['message' => 'Unable to send reset link'],400);

        } catch (ValidationException $e){
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function reset(Request $request) {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email|lowercase',
                'password' => 'required|confirmed|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$*])[A-Za-z\d@#$*]{8,}$/',
            ]);
        
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();
                }
            );
        
            return $status === Password::PASSWORD_RESET ? response()->json(['message' => 'Password reset successfully']): response()->json(['message' => 'Unable to reset password'], 400);

        } catch(ValidationException $e){
            return response()->json(['errors' => $e->errors()],422);
        }
    } 

}
