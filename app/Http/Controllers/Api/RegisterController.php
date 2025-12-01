<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{
    /**

    * Register api

    *

    * @return \Illuminate\Http\Response

    */

    public function register(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'c_password' => 'required|same:password',
                // 'g-recaptcha-response' => 'required' // COMMENTED OUT - reCAPTCHA disabled
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // COMMENTED OUT - reCAPTCHA verification disabled for development
            /*
            // Verify reCAPTCHA
            $recaptchaResponse = $request->input('g-recaptcha-response');
            $secretKey = env('RECAPTCHA_SECRET_KEY');

            if (!$secretKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'reCAPTCHA configuration error'
                ], 500);
            }

            $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip()
            ]);

            $verifyData = $verifyResponse->json();

            if (!$verifyData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'reCAPTCHA verification failed'
                ], 422);
            }
            */

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);

            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;

            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'User registered successfully.'
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }



    /**

     * Login api

     *

     * @return \Illuminate\Http\Response

     */

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $success['token'] = $user->createToken('MyApp')->plainTextToken;
                $success['name'] = $user->name;
                
                return response()->json([
                    'success' => true,
                    'data' => $success,
                    'message' => 'User login successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorised.'
                ], 401);
            }
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}