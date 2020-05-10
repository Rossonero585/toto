<?php


namespace Controllers\Http;

class Controller
{
    protected function sendRequest(int $code, string $content)
    {
        header('Content-type: text/plain', false. $code);
        echo $content;
        die();
    }
}