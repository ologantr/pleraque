<?php
// $Id$
namespace Pleraque;

class RegexUrlBuilder
{
    private $regexString = "#^/";

    private function validateRange(string $range) : void
    {
        try
        {
            new Regex("#[$range]#");
        }
        catch(\InvalidArgumentException $e)
        {
            throw new \InvalidArgumentException("invalid range: $range");
        }
    }

    public function addUrlComponent(string $urlComponent) : self
    {
        $this->regexString .= str_replace("/", "", $urlComponent) . "/";
        return $this;
    }

    public function addFixedStringComponent(string $str) : self
    {
        $this->regexString .= $str;
        return $this;
    }

    public function addSingleCharMatch(string $charRange) : self
    {
        $this->validateRange($charRange);
        $this->regexString .= "([$charRange])";
        return $this;
    }

    public function addStringMatch(string $charRange) : self
    {
        $this->validateRange($charRange);
        $this->regexString .= "([$charRange]+)";
        return $this;
    }

    public function addSlash() : self
    {
        $this->regexString .= "/";
        return $this;
    }

    public function buildRegex() : Regex
    {
        $this->regexString = rtrim($this->regexString, "/");
        $this->regexString .= "$#";
        return new Regex($this->regexString);
    }
}
?>
