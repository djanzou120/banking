<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * @OA\Post(
     *    path="/api/transaction/send",
     *   tags={"Transaction"},
     *   summary="Send money to another account",
     *   description="Send money to another account",
     *   operationId="sendTransaction",
     *   @OA\Parameter(
     *     name="accountIdSender",
     *     required=true,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="accountIdRecipient",
     *     required=true,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="amount",
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
    public function sendToAccount(Request $request)
    {
        try{
            $input = $request->only(['accountIdSender', 'accountIdRecipient', 'amount']);

            $validator = Validator::make($input, [
                'accountIdSender' => 'bail|required|exists:accounts,id',
                'accountIdRecipient' => 'bail|required|exists:accounts,id',
                'amount' => 'bail|numeric',
            ]);
            if ($validator->fails())
                return $this->liteResponse(config("code.request.VALIDATION_ERROR"),$validator->errors());

            // Check sold
            $senderSold = (new Account())->getSold($input['accountIdSender'])->sold;
            if ($senderSold < $input['amount'])
                return $this->liteResponse(config('code.request.FAILURE'), null, 'Insufficient sold');

            $transaction = $this->send($input['accountIdSender'], $input['accountIdRecipient'], $input['amount']);
            if (!empty($transaction))
                return $this->liteResponse(config('code.request.SUCCESS'), $transaction, "Amount send with success.");
            return $this->liteResponse(config('code.request.FAILURE'), null, "Amount not send");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/transaction/deposit",
     *   tags={"Transaction"},
     *   summary="Deposit money to another account",
     *   description="Deposit money to another account",
     *   operationId="depositTransaction",
     *   @OA\Parameter(
     *     name="accountIdRecipient",
     *     required=true,
     *     in="query",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="amount",
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
    public function depositToAccount(Request $request)
    {
        try{
            $input = $request->only(['accountIdRecipient', 'amount']);

            $validator = Validator::make($input, [
                'accountIdRecipient' => 'bail|required|exists:accounts,id',
                'amount' => 'bail|numeric',
            ]);
            if ($validator->fails())
                return $this->liteResponse(config("code.request.VALIDATION_ERROR"),$validator->errors());

            $transaction = $this->deposit($input['accountIdRecipient'], $input['amount']);
            if (!empty($transaction))
                return $this->liteResponse(config('code.request.SUCCESS'), $transaction, "Amount deposit with success.");
            return $this->liteResponse(config('code.request.FAILURE'), null, "Amount not deposit");
        }catch (\Exception $e){
            return $this->liteResponse(config('code.request.EXCEPTION'), null, $e->getMessage());
        }
    }

    public function send($senderId, $receiverId, $amount)
    {
        $transactionSender = $this->init($senderId, Transaction::TYPE_SEND, $amount, ['recipientId' => $receiverId]);
        $transactionReceiver = $this->init($receiverId, Transaction::TYPE_RECEIVE, $amount, ['fromId' => $senderId]);
        $transactionSender->update(['status' => Transaction::STATUS_SUCCESS]);
        $transactionReceiver->update(['status' => Transaction::STATUS_SUCCESS]);

        if (!empty($transactionSender) && !empty($transactionReceiver))
            return $transactionSender;
        return null;
    }

    public function deposit($accountId, $amount)
    {
        $transaction = $this->init($accountId, Transaction::TYPE_DEPOSIT, $amount);

        $transaction->update([
            'depositAgentId' => Auth::user()->id,
            'status' => Transaction::STATUS_SUCCESS
        ]);

        if (!empty($transaction))
            return $transaction;
        return null;
    }

    public function init($accountId, $type, $amount, $option = ['recipientId' => null, 'fromId' => null])
    {
        $data = [
            'status' => Transaction::STATUS_INIT,
            'accountId' => $accountId,
            'type' => $type,
            'amount' => $amount
        ];

        if ($type == Transaction::TYPE_SEND){
            if (!isset($option['recipientId']))
                throw new \Exception("Init Send transaction without recipientId");
            $data['amount'] = -$data['amount'];
            $data['recipientId'] = $option['recipientId'];
        }

        if ($type == Transaction::TYPE_RECEIVE){
            if (!isset($option['fromId']))
                throw new \Exception("Init Send transaction without fromId");
            $data['fromId'] = $option['fromId'];
        }

        $transaction = Transaction::create($data);

        if (!empty($transaction))
            return $transaction;
        throw new \Exception('A Problem on Transaction creation.');
    }
}
