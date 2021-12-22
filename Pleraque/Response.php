<?php
namespace Pleraque;

abstract class Response
{
    protected int $code;

    public function __construct(int $code)
    {
        $this->code = $code;
        $this->setStatusCode();
        $this->setHeaders();
    }

    public static function setBaseHeaders(array $arr) : void
    {
        array_walk($arr, function(string $h, int $index)
        {
            header($h);
        });
    }

    private function setStatusCode() : void
    {
        http_response_code($this->code);
    }

    abstract protected function setHeaders() : void;
    abstract public function return() : void;
}
?>
