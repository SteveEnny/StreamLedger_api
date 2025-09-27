<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginUserRequest;
use App\Http\Requests\V1\RegisterUserRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    use ApiResponses;
    public function register(RegisterUserRequest $request){
        $request_data = [
            ...$request->validated(),
            'password' => Hash::make($request->password),];
        $user = User::Create($request_data);
        return $this->ok('User created successfully', $user, 201);
    }

    public function Login(LoginUserRequest $request) {
        $request->validated($request->all());

        if(!Auth::attempt($request->only('email', 'password'))){
            return $this->error('Invalid credentials', 401);
        }
        $user = User::firstWhere('email', $request->email);
        $data = [
            'token' => $user->createToken('API token for ' . $user->email, ['*'], now()->addMinutes($value = 60))->plainTextToken,
        ];
        return $this->ok('Authenticated', $data);
    }

    public function Logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

    }

    public function user(){
        return $this->ok('User data', Auth::user());
    }

}