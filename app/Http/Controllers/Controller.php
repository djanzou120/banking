<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResponseParser\Builder;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @license Apache 2.0
 */

/**
 * @OA\Info(
 *     description="Banking Test Application",
 *     version="1.0.0",
 *     title="Banking Test",
 *     termsOfService="http://swagger.io/terms/",
 *     @OA\Contact(
 *         email="apiteam@swagger.io"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\SecurityScheme(
 *   description="User token example { Bearer <token-value> }",
 *   securityScheme="bearer",
 *   type="http",
 *   in="header",
 *   scheme="bearer",
 *   name="Authorization"
 * )
 *
 * @OA\OpenApi(
 *   security={
 *     {"x_api_key":{"type":"apiKey","name":"Authorization","in":"header"}}
 *   }
 * )
 *
 */

/**
 *
 * * @OA\Tag(
 *     name="Response code",
 *     description="
 *     'TOKEN_EXPIRED' => 1, 'BLACK_LISTED_TOKEN' => 2, 'INVALID_TOKEN' => 3, 'NO_TOKEN' => 4,
 *     'USER_NOT_FOUND' => 5,
 *     'WRONG_JSON_FORMAT' => 6,
 *     'SUCCESS' => 1000, 'FAILURE' => 1001, 'VALIDATION_ERROR' => 1002, 'EXPIRED' => 1003, 'DATA_EXIST' => 1004,
 *     'NOT_AUTHORIZED' => 1005,
 *     'ACCOUNT_NOT_VERIFY' => 1100,'WRONG_USERNAME' => 1101,'WRONG_PASSWORD' => 1102,'WRONG_CREDENTIALS' => 1103,
 *     'ACCOUNT_VERIFIED' => 1104,'NOT_EXISTS' => 1105"
 * )
 * @OA\ExternalDocumentation(
 *     description="Find out more about Swagger",
 *     url="http://swagger.io"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const TRANSACTION = 'TRA';
    public const ACCOUNT = 'ACC';
    public const CUSTOMER = 'CUS';

    /**
     * Default validator in case of non specification
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(&$data)
    {
        return Validator::make($data, ['*' => 'required']);
    }

    /**
     * parsing api response according the specification
     * @param      $code
     * @param null $data
     * @param null $message
     * @param null $token
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function liteResponse($code, $data = null, $message = null, $token = null)
    {
        $isJsonResponse = request()->isJson() or request()->isXmlHttpRequest() or request()->ajax();

        $builder = new Builder($code, $message);
        $builder->setData($data);
        $builder->setToken($token);
        return $isJsonResponse ? response()->json($builder->reply()) : $builder->reply();
    }
}
