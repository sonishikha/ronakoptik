<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use Exception;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiValidation extends Model
{
    public function validateAndGetUser($request, $offset = false, $sync = false)
    {
        $validator = $this->validateRequest($request, $offset, $sync);
        if($validator->fails()){
            throw New Exception($validator->messages()->first());
        }
        
        $user = $this->checkUserExistsByEmail($request->userName);
        if(!$user) {
            throw new Exception('Username Not Found');
        };
        
        return $user;
    }

    public function validateRequest($request, $offset, $sync)
    {
        $validator = Validator::make($request->all(), 
                    array(
                        'userName' => 'required|email',
                    )
        );
        $validator->sometimes('offSet', 'required|int', function () use ($offset) {
            return $offset === true;
        });
        $validator->sometimes('sync', ['required', Rule::in(['no','yes','No','Yes','NO','YES']),], function () use ($sync) {
            return $sync === true;
        });
        return $validator;
    }

    public function checkUserExistsByEmail($email)
    {
        $authentcated_user = $this->getAuthenticatedUser();
        $authenticated_email = $authentcated_user->getData()->user->email;
        if($authenticated_email != $email){
            throw New Exception('Invalid Email Id. Unauthenticated User.');
        }
        return User::where('email', '=', $email)->first();
    }

    public function getUserCustomerRegionGroup($user_id)
    {
        return User::find($user_id)->group_code;
    }

    public function getUserBrand($user_id)
    {
        return User::find($user_id)->brand;
    }

    public function getUserWarehouse($user_id)
    {
        return User::find($user_id)->warehouse;
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

}
