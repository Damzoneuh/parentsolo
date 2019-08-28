<?php


namespace App\Mailer;

use App\Entity\User;

class Mailing
{
    private $_mailer;
    private $_message;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->_mailer = $mailer;
        $this->_message = new \Swift_Message();
    }

    public function sendConfirmMessage(User $user, $token) : void {
        $this->_message->setSubject('Registration ParentSolo');
        $this->_message->setTo($user->getEmail());
        $this->_message->setFrom('noreply@parentsolo.ch');
        $this->_message->setBody('
            <body>
                <h1>Confirmation for registration  to parentsolo.ch</h1>
                <p>Please click on the button to register finally</p>
                <a class="btn btn-primary btn-group" href="https://parentsolo.backndev.fr/register/' . $token . '">Register</a>
            </body>', 'text/html');
        $this->_mailer->send($this->_message);
    }

    public function sendResetMessage($email, $token){
        $this->_message->setSubject('Reset password ParentSolo');
        $this->_message->setTo($email);
        $this->_message->setFrom('noreply@parentsolo.ch');
        $this->_message->setBody('
            <body>
                <h1>Reseting password for ParentSolo</h1>
                <p>Please click on the button to reset your password</p>
                <a class="btn btn-primary btn-group" href="https://parentsolo.backndev.fr/reset/browse/' . $token . '">Reset</a>
            </body>', 'text/html');
        $this->_mailer->send($this->_message);
    }
}