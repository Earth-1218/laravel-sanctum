<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPEmail;
use App\Models\TempOtp;
use IWantTo;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return Token
     */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'User not created',
                ], 401);
            }

            TempOtp::create([
                'user_id' => $user->id,
                'otp' => rand(1111,9999),
                'expires_at' => null
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Request OTP
     * @param Request $request
     * @return 'OTP sent successfully.'
     */
    public static function requestOtp(Request $request){
        try {

            $user = User::where('email', $request->email)->first();
    
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 401);
            }
            
            $tempAuth = TempOtp::where('user_id',$user->id)->first();
            $tempAuth->expires_at = now()->addMinutes(10);
            $tempAuth->save();
            $tempAuth->refresh();
    
            Mail::to($user->email)->send(new OTPEmail($tempAuth->otp));
    
            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verify OTP
     * @param email
     * @param OTP
     * @return Token
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|digits_between:4,4|numeric|min:1111|max:9999'                
            ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            
            $user = User::where('email',$request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 401);
            }

            // Check if the provided OTP matches an entry in the temporary OTP table
            $tempOtp = TempOtp::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('expires_at', '>=', now())
                ->first();

            if (!$tempOtp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired OTP.',
                ], 401);
            }

            TempOtp::where('id', $tempOtp->id)->update([
                'otp' => rand(1111,9999),
                'expires_at' => null
            ]);

            // Generate and return an access token
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


     /**
     * If user has token
     * @param token
     * @return 'Hello World'
     */
    public function helloWorld(){
        if(!auth()->user()){
            return response()->json([
                'status' => false,
                'message' => 'uauthorized!'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => IWantTo::displayHelloWorld()
        ], 200);
    }

}
