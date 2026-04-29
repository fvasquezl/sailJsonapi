<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use LaravelJsonApi\Testing\MakesJsonApiRequests;

abstract class TestCase extends BaseTestCase
{
    use CreateData, MakesJsonApiRequests {
        MakesJsonApiRequests::jsonApi as public;
    }

}
