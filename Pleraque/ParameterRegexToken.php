<?php
namespace Pleraque;

class ParameterRegexToken extends UriToken
{
    private $regex;

    public function __construct(string $token, string $regex)
    {
        parent::__construct($token);
        $this->regex = $regex;
        $this->validateRegex();
    }

    private function validateRegex() : void
    {
        (new Regex("#{$this->regex}#"));
    }

    public function __toString()
    {
        return "({$this->regex})";
    }
}
?>
