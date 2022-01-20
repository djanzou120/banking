<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetHistoryAccountTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/api/account/story/1',
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
