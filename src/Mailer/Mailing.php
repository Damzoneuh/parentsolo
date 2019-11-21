<?php


namespace App\Mailer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class Mailing
{
    private $_mailer;
    private $_message;

    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer)
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

    public function sendException(ExceptionEvent $event){
        $e = $event->getException();
        $message = new \Swift_Message();
        $message->setSubject('Exception');
        $message->setTo('damien@backndev.fr');
        $message->setFrom('exception@parentsolo.ch');
        $message->setBody('
            <h1>Exception Caught</h1>
            <p>message : '. $e->getMessage() . '</p>
            <p>Line : ' .$e->getLine() .'</p>
            <p>error code : ' . $e->getCode()  . '</p>
            <p>stack trace : ' . $e->getTraceAsString() . '</p>
           
        ', 'text/html');
        $this->_mailer->send($message);
    }

    public function sendMessageReceived(User $user, User $target){
        $message = $this->_message;
        $message->setTo($target->getEmail());
        $message->setFrom('noreply@parentsolo.ch');
        $message->setSubject('New message');
        $message->setBody('
            <h1>You have receive a new message</h1>
            <p>' . $user->getPseudo() . ' sent a message for you !</p>
        ', 'text/html');
        $this->_mailer->send($message);
    }

    public function sendNotification(User $user, $action, $content){
        $message = $this->_message;
        $message->setTo($user->getEmail());
        $message->setFrom('noreply@parentsolo.ch');
        $message->setSubject($action);
        $message->setBody('<p> ' . $content . '</p>');
        $this->_mailer->send($message);
    }
}