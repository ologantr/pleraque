<?php
namespace Pleraque;
use Pleraque\Utils as U;

class ParameterRegexToken extends UriToken
{
    private string $regex;

    public function __construct(string $token, string $regex)
    {
        parent::__construct($token);
        $this->regex = $regex;
        $this->validateRegex();
    }

    private function validateRegex() : void
    {
        (new U\Regex("#{$this->regex}#"));
    }

    public function __toString() : string
    {
        return "({$this->regex})";
    }
}
?>
