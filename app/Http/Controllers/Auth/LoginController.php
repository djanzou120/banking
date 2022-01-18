<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *   tags={"Auth"},
     *   summary="Logs user into the system",
     *   description="Generate user token by login in the system",
     *   operationId="loginUser",
     *   @OA\Parameter(
     *     name="email",
     *     required=true,
     *     in="query",
     *     description="The user name for login max:60",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The password for login in clear text min:6, max:20",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *   ),
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            $validator = $this->validator($credentials);

            if ($validator->fails())
                return $this->liteResponse(config("code.request.VALIDATION_ERROR"),$validator->errors());

            if (!$token = JWTAuth::attempt($credentials))
                return $this->liteResponse(config('code.auth.WRONG_CREDENTIALS'), null, 'Invalid email or password');

            JWTAuth::setToken($token);
            $user = JWTAuth::toUser();
            return $this->liteResponse(config('code.request.SUCCESS'),  $user, "User connected with success.", $token);
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            'email' => 'bail|required|email|max:60',
            'password' => 'bail|required|min:6|max:20',
        ]);
    }
}
