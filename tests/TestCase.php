<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers = array_merge(['Accept' => 'application/vnd.api+json'], $headers);

        return parent::json($method, $uri, $data, $headers, $options);
    }
}
