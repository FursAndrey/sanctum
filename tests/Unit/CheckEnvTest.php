<?php

namespace Tests\Unit;

use App\Actions\checkEnvIsProdAction;
use PHPUnit\Framework\TestCase;

class CheckEnvTest extends TestCase
{
    public function test_that_env_name_is_prod(): void
    {
        $this->assertTrue(true, (new checkEnvIsProdAction)('production'));
    }

    public function test_that_env_name_is_not_prod(): void
    {
        $this->assertFalse(false, (new checkEnvIsProdAction)('testing'));
        $this->assertFalse(false, (new checkEnvIsProdAction)('local'));
    }
}
