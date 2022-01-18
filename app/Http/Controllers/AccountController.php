<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/account",
     *   tags={"Account"},
     *   summary="Find all accounts",
     *   description="",
     *   operationId="allAccount",
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *     @OA\Header(
     *       header="X-Rate-Limit",
     *       @OA\Schema(
     *           type="integer",
     *           format="int32"
     *       ),
     *       description="calls per hour allowed by the user"
     *     )
     *   ),
     * )
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        try {
            $accounts = Account::paginate(15);
            return $this->liteResponse(config('code.request.SUCCESS'), $accounts, "Accounts list");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *    path="/api/account/{accountId}",
     *   tags={"Account"},
     *   summary="Search account by ID",
     *   description="Search account by ID",
     *   operationId="showAccount",
     *   @OA\Parameter(
     *     name="accountId",
     *     required=true,
     *     in="path",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *     @OA\Header(
     *       header="X-Rate-Limit",
     *       @OA\Schema(
     *           type="integer",
     *           format="int32"
     *       ),
     *       description="calls per hour allowed by the user"
     *     )
     *   ),
     * )
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(Request $request, string $accountId)
    {
        try {
            $account = Account::find($accountId);
            if (empty($account))
                return $this->liteResponse(config('code.request.FAILURE'), null, "Account not found");
            return $this->liteResponse(config('code.request.SUCCESS'), $account, "Account found");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/account",
     *   tags={"Account"},
     *   summary="Create a account",
     *   description="Create a account",
     *   operationId="createAccount",
     *   @OA\Parameter(
     *     name="customerId",
     *     required=true,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     required=false,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *     @OA\Header(
     *       header="X-Rate-Limit",
     *       @OA\Schema(
     *           type="integer",
     *           format="int32"
     *       ),
     *       description="calls per hour allowed by the user"
     *     )
     *   ),
     * )
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        try {
            $input = $request->only((new Account())->getFillable());

            $validator = $this->validator($input);
            if ($validator->fails())
                return $this->liteResponse(config("code.request.VALIDATION_ERROR"),$validator->errors());

            $input['id'] = $this->genId(self::ACCOUNT);
            $account = Account::create($input);
            if (!empty($account))
                return $this->liteResponse(config('code.request.SUCCESS'), $account, "Account created with success.");
            return $this->liteResponse(config('code.request.FAILURE'), $account, "Account not created");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *    path="/api/account/{accountId}",
     *   tags={"Account"},
     *   summary="Update a account",
     *   description="Update a account by ID",
     *   operationId="updateAccount",
     *   @OA\Parameter(
     *     name="customerId",
     *     required=false,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     required=false,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *     @OA\Header(
     *       header="X-Rate-Limit",
     *       @OA\Schema(
     *           type="integer",
     *           format="int32"
     *       ),
     *       description="calls per hour allowed by the user"
     *     )
     *   ),
     * )
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $accountId)
    {
        try {
            $input = $request->only((new Account())->getFillable());

            $validator = Validator::make($input, [
                'customerId' => 'bail|exists:customers,id',
                'name' => 'bail|max:120'
            ]);
            if ($validator->fails())
                return $this->liteResponse(config("code.request.VALIDATION_ERROR"),$validator->errors());

            $account = Account::find($accountId);
            if (empty($account))
                return $this->liteResponse(config('code.request.FAILURE'), "Account not found");

            $account->update($input);
            return $this->liteResponse(config('code.request.SUCCESS'), $account, "Account updated with success.");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Delete (
     *    path="/api/account/{accountId}",
     *   tags={"Account"},
     *   summary="Delete a account",
     *   description="Delete a account by ID",
     *   operationId="deleteAccount",
     *   @OA\Parameter(
     *     name="customerId",
     *     required=true,
     *     in="path",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string"),
     *     @OA\Header(
     *       header="X-Rate-Limit",
     *       @OA\Schema(
     *           type="integer",
     *           format="int32"
     *       ),
     *       description="calls per hour allowed by the user"
     *     )
     *   ),
     * )
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function delete($accountId)
    {
        try {
            if (!Auth::user()->isAuthorized(Role::ROOT))
                return $this->liteResponse(config('code.request.NOT_AUTHORIZED'));

            $account = Account::find($accountId);
            if (empty($account))
                return $this->liteResponse(config('code.request.FAILURE'), null, "Account not found.");
            $account->delete();
            return $this->liteResponse(config('code.request.SUCCESS'), null, "Account deleted with success.");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            'customerId' => 'bail|required|exists:customers,id',
            'name' => 'bail|max:120'
        ]);
    }
}
