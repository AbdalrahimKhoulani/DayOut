<?php

namespace App\Http\Controllers\Api;

use Aloha\Twilio\Twilio;
use App\Http\Controllers\Controller;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Model\SendMessageRequest;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Exception;

class SmsController extends Controller
{
    public function test()
    {

        // Configure client
        $config = Configuration::getDefaultConfiguration();
        $config->setApiKey('Authorization', 'your-token-here');
        $apiClient = new ApiClient($config);
        $messageClient = new MessageApi($apiClient);

// Sending a SMS Message
        $sendMessageRequest1 = new SendMessageRequest([
            'phoneNumber' => '07791064781',
            'message' => 'test1',
            'deviceId' => 1
        ]);
        $sendMessageRequest2 = new SendMessageRequest([
            'phoneNumber' => '07791064781',
            'message' => 'test2',
            'deviceId' => 2
        ]);
        $sendMessages = $messageClient->sendMessages([
            $sendMessageRequest1,
            $sendMessageRequest2
        ]);
        print_r($sendMessages);

    }
}
