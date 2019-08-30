<?php


namespace App\Process;

use Symfony\Component\Process\Process;

class SixProcess
{
    public static function capturePayments(){
        $process = new Process(['php', 'console', 'payment:capture']);
        $process->setWorkingDirectory('/var/www/html/bin/');
        $process->start();
    }
}