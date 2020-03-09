<?php

namespace Classes;

use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class SMS
{

    public function sendSMS($phone, $message)
    {

        try {

            // Send an SMS using Twilio's REST API and PHP
            $sid = "YOURSID"; // Your Account SID from www.twilio.com/console
            $token = "YOURTOKEN"; // Your Auth Token from www.twilio.com/console
            $twilio_number = 'YOURTWILIONUMBER'; // Your twilio number

            $client = new Client($sid, $token);
            $message = $client->messages->create(
                $phone, // Text this number
                array(
                    'from' => $twilio_number, // From a valid Twilio number
                    'body' => $message,
                )
            );

            return $this->response(true, 'Message sent');

        } catch (RestException $e) {
            return $this->response(false, $e->getMessage());
        }

    }

    protected function response($status, $message)
    {
        return [

            'status' => $status,
            'message' => $message,

        ];
    }

}
