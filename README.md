# msg91/msg91

## Introduction

`msg91/msg91` is a Laravel PHP package that provides functionality to integrate MSG91 services into your Laravel applications.

## Installation

You can install this package via Composer. Run the following command in your Laravel project directory:

```bash
composer require msg91/msg91
```

## Once the package is installed, you can use it in your Laravel application for OTP verification as follows:

```bash
use Msg91\Services\OTPService;

// Import the OTPService class
$otpService = new OTPService(); // Instantiate the OTPService class

$authKey = 'your_auth_key';
$token = 'access-token'; // Replace with the actual access token to verify

$response = $otpService->verifyToken($authKey, $token);

// Handle the response
// Example:
if ($response['type'] === 'success') {
    echo 'OTP verification successful';
} else {
    echo 'OTP verification failed: ' . $response['message'];
}


