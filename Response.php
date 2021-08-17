<?php
namespace Pleraque;

abstract class Response
{
    protected $code;

    public function __construct(int $code)
    {
        $this->code = $code;
        $this->setStatusCode();
        $this->setHeaders();
    }

    private function setStatusCode() : void
    {
        http_response_code($this->code);
    }

    protected function setHeaders() : void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
    }

    abstract public function return() : void;
}
?>
