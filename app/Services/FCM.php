<?php

namespace App\Services;

class FCM
{

    private function getTokens($users)
    {
        $tokens = [];

        foreach ($users as $user) {
            if ($user->mobile_token != null) {
                array_push($tokens,$user->mobile_token);
            }
        }
        return $tokens;
    }

    public function sendNotification($users, $title, $body)
    {

        $tokens = $this->getTokens($users);

        $SERVER_API_KEY = 'AAAASWsX8NI:APA91bHB9RYjymdjEjDrqGoFcn_cZZGRhFiDk9iBCWo1bLHEqcOdwmR7WEcETHPAzwFwBikHG__8h7QayxXGokY1eoY4AJ0RYcjNQ5xlj2r83bBPe3rYlsh6JOs8OPNowiSjOjY-7ApH';
//        $token_1 = 'Test Token';
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default" // required for sound on ios
            ],
        ];

        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);

        if ($response) {
            storeNotifications($users, $title, $body);
        }

//        dd($response);
    }



    private function storeNotifications($users, $title, $body)
    {
        foreach ($users as $user) {
            Notification::create([
                'title' => $title,
                'body'=>$body,
                'user_id'=>$user->id
            ]);
        }


    }


}
