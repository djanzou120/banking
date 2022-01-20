<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A Login Test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->post('/api/auth/login', [
            'email' => 'root@banking.com',
            'password' => 'password'
        ]);

        $res = json_decode($response->getContent());

        dump($res);

        if ($res->code == config('code.request.SUCCESS'))
            return $response->assertOk();
        return $response->assertForbidden();
    }
}
