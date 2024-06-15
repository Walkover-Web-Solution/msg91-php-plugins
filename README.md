# msg91/msg91

## Introduction

`msg91/msg91` is a Laravel PHP package that provides functionality to integrate MSG91 services into your Laravel applications.

## Installation

You can install this package via Composer. Run the following command in your Laravel project directory:

```bash
composer require msg91/msg91
```

## USAGE

# OTP VERIFICATION
Once the package is installed, you can use it in your Laravel application for OTP verification as follows:

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
```

## Campaign Service
You can also use this package to run campaigns using the CampaignService. 
Below is an example of how to use the CampaignService in your Laravel application:

```bash
use Msg91\Services\CampaignService;

// Instantiate the CampaignService class
$service = new CampaignService('your_auth_key');

// Define your campaign slug and input data : max 1000 including cc and bcc as individual entity
$campaignSlug = 'your_campaign_slug';
$inputData = [
    "data" => [
        [
            "to" => 'recipient1@example.com',
            "cc" => 'cc1@example.com',
            "bcc" => 'bcc1@example.com',
            "mobiles" => '919876543210',
            "name" => 'Recipient 1',
            "from_name" => 'Sender Name',
            "from_email" => 'sender@example.com',
            "variables" => [
                "var1" => 'value1',
                "var2" => 'value2',
            ],
        ],
        [
            "to" => 'recipient1@example.com',
            "cc" => 'cc1@example.com',
            "bcc" => 'bcc1@example.com',
            "mobiles" => '919876543210',
            "name" => 'Recipient 1',
            "from_name" => 'Sender Name',
            "from_email" => 'sender@example.com',
            "variables" => [
                "var1" => 'value1',
                "var2" => 'value2',
            ],
        ], ...
];

// Run the campaign
$response = $service->runCampaign($campaignSlug, $inputData);

// Handle the response
var_dump($response);
```

## License
This package is open-sourced software licensed under the MIT license.