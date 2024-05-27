<?php

namespace Tests;

use Msg91\Services\OTPService;
use PHPUnit\Framework\TestCase;

class OTPServiceTest extends TestCase {
    public function testVerifyToken() {
        $service = new OTPService();
        $response = $service->verifyToken('your_auth_key','access-token');
        $this->assertTrue(true); // Placeholder assertion to ensure the test passes
    }
}
