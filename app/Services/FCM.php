<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class FCM
{

    private function getTokens($users)
    {

        $tokens = [];

        if ($users instanceof User) {
            if ($users->mobile_token != null) {

                array_push($tokens, $users->mobile_token);
            }
        } else {
            foreach ($users as $user) {
                if ($user->mobile_token != null) {

                    array_push($tokens, $user->mobile_token);
                }
            }
        }

        return $tokens;
    }

    public function sendNotification($users, $title, $body)
    {

        $tokens = $this->getTokens($users);


        $SERVER_API_KEY = 'AAAARMT3glg:APA91bFoDA_1z1XjPlgnHqS9nneNxE_Xl4u5HaTtotF1Hq4woVVnBwlsQ1EEUQAfiH-hRk85Vn6hHauvmYp1RH8d15EuLKR0jbJDw6nq92GGTDrztkT6NLP3CpLWapyp-AO94v88luws';
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
            $this->storeNotifications($users, $title, $body);
        }
        error_log($response);
//        dd($response);
    }


    private function storeNotifications($users, $title, $body)
    {
        foreach ($users as $user) {
            Notification::create([
                'title' => $title,
                'body' => $body,
                'user_id' => $user->id
            ]);
        }


    }


}
