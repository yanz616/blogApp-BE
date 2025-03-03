<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Claims\Expiration;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $params = $request->all();
            $validator = Validator::make(
                $params,
                [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6|confirmed',
                    'role' => 'sometimes|in:admin,user',
                ],
                [
                    'name.required' => 'Name is required',
                    'email.required' => 'Email is required',
                    'email.email' => 'Email must be a valid email address',
                    'email.unique' => 'Email is already in use',
                    'password.required' => 'Password is required',
                    'password.min' => 'Password must be at least :min characters',
                    'password.confirmed' => 'Password confirmation does not match',
                ]
            );

            if ($validator->fails()) {
                return response()->json(ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all()), 400);
            }

            // Create the user
             User::create([
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => Hash::make($params['password']),
                'role' => $request->role ?? 'user',
            ]);

            // Respond with success
            return response()->json(ApiFormatter::createJson(201, 'Registration successful'), 201);
        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage()), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $params = $request->all();

            //validasi Input
            $validator = Validator::make(
                $params,
                [
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                ],
                [
                    'email.required' => 'Email is Required',
                    'email.email' => 'Email must be Valid email Adress',
                    'password.required' => 'Password is Required',
                    'password.min' => 'Password must be at least :min characters',
                ],
            );

            if ($validator->fails())
                return response()->json(ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all()), 400);

            //cari user berdasarkan email
            $user = User::where('email', $params['email'])->first();
            if (!$user)
                return response()->json(ApiFormatter::createJson(404, 'Account Not Found'), 404);

            //periksa password
            if (!Hash::check($params['password'], $user->password))
                return response()->json(ApiFormatter::createJson(401, 'password does not match'), 401);

            //generate token jwt
            if (!$token = JWTAuth::FromUser($user))
                return response()->json(ApiFormatter::createJson(500, 'Failed to Generate Token'), 500);

            //informasi token
            $currentDateTime = Carbon::now();
            $expirationDateTime = $currentDateTime->addSeconds(JWTAuth::factory()->getTTL() * 60);

            $info = [
                'type' => 'bearer',
                'token' => $token,
                'expires' => $expirationDateTime->format('Y-m-d H:i:s'),
            ];
            return response()->json(ApiFormatter::createJson(200, 'success', $info), 200);
        } catch (\Exception $e) {
            return response()->Json(ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage()), 500);
        }
    }

    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload();

            $expiration = $payload->get('exp');
            $expiration_time = date('Y-m-d H:i:s', $expiration);

            $data['name'] = $user['name'];
            $data['email'] = $user['email'];
            $data['exp'] = $expiration_time;

            return response()->json(ApiFormatter::createJson(200, 'Logged in User', $data), 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(ApiFormatter::createJson(401, 'Token expired'), 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(ApiFormatter::createJson(401, 'Token invalid'), 401);
        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage()), 500);
        }
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            $expiration = JWTAuth::factory()->getTTL() * 60;
            $expirationDateTime = Carbon::now()->addSeconds($expiration);

            $info = [
                'type' => 'Bearer',
                'token' => $newToken,
                'expires' => $expirationDateTime->format('Y-m-d H:i:s'),
            ];

            return response()->json(ApiFormatter::createJson(200, 'Successfully Refreshed', $info), 200);
        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, 'Token refresh failed', $e->getMessage()), 500);
        }
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(ApiFormatter::createJson(200, 'Succes Logout'), 200);
    }
}
