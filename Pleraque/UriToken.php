<?php
namespace Pleraque;

class UriToken implements \Stringable
{
    private string $tokenName;

    public function __construct(string $tokenName)
    {
        $this->tokenName = $tokenName;
    }

    public function __toString() : string
    {
        return $this->tokenName;
    }

    public function getName() : string
    {
        return $this->tokenName;
    }
}
?>
