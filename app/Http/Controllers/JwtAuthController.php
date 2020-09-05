<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;

class JwtAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could Not Create Token'], 500);
        }
        JWTAuth::setToken($token);
        $token_obj = JWTAuth::getToken();
        $payload = JWTAuth::decode($token_obj)->toArray();
        $insert_response = $this->storeJwtAuthAttemptData($token, $payload, $request);
        $res_json = json_decode($insert_response, true)['insert_array']; 
        return response()->json([
                            'user_id' => $res_json['user_id'],
                            'email' => $res_json['email'],
                            'instance_url' => $res_json['instance_url'],
                            'token_type' => $res_json['token_type'],
                            'access_token' => $res_json['access_token'],
                            'issued_at' => $res_json['issued_at'],
                        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'email' => $request->get('email'),
            'password' => md5($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

    protected function storeJwtAuthAttemptData($token, $payload, $request){
        $insert_array = [
            'user_id' => $payload['sub'],
            'email' => $payload['email'],
            'instance_url' => $payload['iss'],
            'token_type' => 'Bearer',
            'access_token' => $token,
            'device_key' => $request->device_key,
            'agent_type' => $request->server('HTTP_USER_AGENT'),
            'attempt_flag' => 1,
            'issued_at' => date('Y-m-d H:i:s', $payload['iat']),
            'expire_at' => date('Y-m-d H:i:s', $payload['exp']),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $insert_id = DB::connection('mysql')->table('ro_core_user_auth')->insertGetId($insert_array);
        return json_encode(['insert_id'=>$insert_id,'insert_array'=>$insert_array]);
    }

}

