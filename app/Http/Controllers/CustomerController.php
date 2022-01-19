<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    /**
     * @OA\Get(
     *    path="/api/customer",
     *   tags={"Customer"},
     *   summary="Find all customers",
     *   description="",
     *   operationId="allCustomer",
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
            $customers = Customer::paginate(15);
            return $this->liteResponse(config('code.request.SUCCESS'), $customers, "Customers list");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *    path="/api/customer/{customerId}",
     *   tags={"Customer"},
     *   summary="Search customer by ID",
     *   description="Search customer by ID",
     *   operationId="showCustomer",
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
    public function show($customerId)
    {
        try {
            $customer = Customer::find($customerId);
            if (empty($customer))
                return $this->liteResponse(config('code.request.FAILURE'), null, "Customer not found");
            return $this->liteResponse(config('code.request.SUCCESS'), $customer, "Customer found");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/customer",
     *   tags={"Customer"},
     *   summary="Create a customer",
     *   description="Create a customer",
     *   operationId="createCustomer",
     *   @OA\Parameter(
     *     name="firstname",
     *     required=true,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="lastname",
     *     required=false,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
     *     required=true,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="initialAmount",
     *     required=true,
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
    public function store(Request $request)
    {
        try {
            $initialAmount = $request->input('initialAmount');
            $input = $request->only((new Customer())->getFillable());

            $validator = $this->validator($input);
            if ($validator->fails())
                return $this->liteResponse(config("code.request.VALIDATION_ERROR"),$validator->errors());

            $input['createdById'] = Auth::user()->id;

            $customer = Customer::create($input);

            if (!empty($customer)){
                $account = Account::create(['customerId' => $customer->id]);
                if (!empty($account)){
                    $transaction = (new TransactionController())->deposit($account->id, $initialAmount);
                    if (!empty($transaction))
                        return $this->liteResponse(config('code.request.SUCCESS'), ['customer' => $customer, 'transaction' => $transaction], "Customer created with success.");
                    return $this->liteResponse(config('code.request.FAILURE'), null, "An error occured on initial deposit");
                }
                return $this->liteResponse(config('code.request.FAILURE'), null, "An error occured on account creation");
            }
            return $this->liteResponse(config('code.request.FAILURE'), $customer, "Customer not created");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *    path="/api/customer/{customerId}",
     *   tags={"Customer"},
     *   summary="Update a customer",
     *   description="Update a customer by ID",
     *   operationId="updateCustomer",
     *   @OA\Parameter(
     *     name="firstname",
     *     required=false,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="lastname",
     *     required=false,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
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
    public function update(Request $request, $customerId)
    {
        try {
            $input = $request->only((new Customer())->getFillable());

            $validator = Validator::make($input, [
                'firstname' => 'bail|max:120',
                'lastname' => 'bail|max:120',
                'phone' => 'bail|min:9|max:14|unique:customers,phone',
            ]);
            if ($validator->fails())
                return $this->liteResponse(config("code.request.VALIDATION_ERROR"),$validator->errors());

            $customer = Customer::find($customerId);
            if (empty($customer))
                return $this->liteResponse(config('code.request.FAILURE'), "Customer not found");

            $customer->update($input);
            return $this->liteResponse(config('code.request.SUCCESS'), $customer, "Customer updated with success.");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Delete (
     *    path="/api/customer/{customerId}",
     *   tags={"Customer"},
     *   summary="Delete a customer",
     *   description="Delete a customer by ID",
     *   operationId="deleteCustomer",
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
    public function delete($customerId)
    {
        try {
            if (!Auth::user()->isAuthorized(Role::ROOT))
                return $this->liteResponse(config('code.request.NOT_AUTHORIZED'));

            $customer = Customer::find($customerId);
            if (empty($customer))
                return $this->liteResponse(config('code.request.FAILURE'), null, "Customer not found.");
            $customer->delete();
            return $this->liteResponse(config('code.request.SUCCESS'), null, "Customer deleted with success.");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *    path="/api/customer/sold/{customerId}",
     *   tags={"Customer"},
     *   summary="Sold of customer by ID",
     *   description="Sold of  customer by ID",
     *   operationId="soldCustomer",
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
    public function getSold($customerId)
    {
        try {
            $customer = (new Account())->getCustomerSold($customerId);
            return $this->liteResponse(config('code.request.SUCCESS'), $customer);
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *    path="/api/customer/subsold/{customerId}",
     *   tags={"Customer"},
     *   summary="Subsold of customer by ID",
     *   description="Subsold of  customer by ID",
     *   operationId="subSoldCustomer",
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
    public function getSubSold($customerId)
    {
        try {
            $customer = (new Account())->getCustomerSubSold($customerId);
            return $this->liteResponse(config('code.request.SUCCESS'), $customer);
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    protected function validator(&$data)
    {
        return Validator::make($data, [
            'firstname' => 'bail|required|max:120',
            'lastname' => 'bail|max:120',
            'phone' => 'bail|required|min:9|max:14|unique:customers,phone',
        ]);
    }
}
