<?php
namespace Pleraque;

abstract class ResponseWithBody extends Response
{
    protected $body;

    public function __construct(int $code, $body)
    {
        parent::__construct($code);
        $this->body = $body;
    }

    abstract public function return() : void;
}
?>
