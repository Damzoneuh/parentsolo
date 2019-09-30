<?php


namespace App\Service;


use App\Entity\User;
use App\Mailer\Mailing;

class MailingService extends Mailing
{
    public function sendUnconnectedMail(User $user, User $target){
        $this->sendMessageReceived($user, $target);
    }

    public function sendRegistrationConfirmationMail(User $user, $token){
        $this->sendConfirmMessage($user, $token);
    }
}