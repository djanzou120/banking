<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransfertTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->post('/api/transaction/send',
            [
                'accountIdSender' => 1,
                'accountIdRecipient' => 2,
                'amount' => 200
            ],
            [
                'Authorization' => self::Authorization
            ]);

        $res = json_decode($response->getContent());

        dump($res);

        if ($res->code == config('code.request.SUCCESS'))
            return $response->assertOk();
        return $response->assertForbidden();
    }
}
