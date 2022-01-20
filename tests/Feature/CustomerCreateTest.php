<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Nette\Utils\Random;
use Tests\TestCase;

class CustomerCreateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->post('/api/customer', [
            'firstname' => 'Test',
            'phone' => random_int(600000000, 999999999),
            'initialAmount' => 2000
        ], [
            'Authorization' => self::Authorization
        ]);

        $res = json_decode($response->getContent());

        dump($res->data);

        if ($res->code == config('code.request.SUCCESS'))
            return $response->assertOk();
        return $response->assertForbidden();
    }
}
