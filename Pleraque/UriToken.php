<?php
namespace Pleraque;

class UriToken
{
    private $tokenName;

    public function __construct(string $tokenName)
    {
        $this->tokenName = $tokenName;
    }

    public function __toString()
    {
        return $this->tokenName;
    }

    public function getName() : string
    {
        return $this->tokenName;
    }
}
?>
