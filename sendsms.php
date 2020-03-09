<?php

use Classes\SMS;

$body = file_get_contents('php://input');

if(!$body)
{
      echo 'Cheating?';
      die();
}

header('Content-type: application/json');

require_once './vendor/autoload.php';

$form = json_decode($body);

$sms = new SMS;

echo json_encode( $sms->sendSMS($form->formattedPhone, $form->message) );

