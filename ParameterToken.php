<?php
namespace Pleraque;

class ParameterToken extends ParameterRegexToken
{
    public function __construct(string $paramName)
    {
        parent::__construct($paramName, "[A-Za-z0-9\_\-]+");
    }
}
?>
