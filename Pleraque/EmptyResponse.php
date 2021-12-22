<?php
namespace Pleraque;

class EmptyResponse extends Response
{
    public function setHeaders() : void
    {
        return;
    }

    public function return() : void
    {
        return;
    }
}
?>
