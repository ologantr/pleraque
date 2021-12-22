<?php
namespace Pleraque\Utils;

final class Regex implements \Stringable
{
    private string $regex;
    private array $matches = [];

    public function __construct(string $regex)
    {
        $this->regex = $regex;
        if(@preg_match($this->regex, "") === false)
            throw new \InvalidArgumentException("invalid regex: $regex");
    }

    public function __toString()
    {
        return $this->regex;
    }

    public function match(string $str) : bool
    {
        return preg_match($this->regex, $str);
    }

    public function getMatches(string $str) : array
    {
        preg_match($this->regex, $str, $this->matches);
        array_shift($this->matches);
        return $this->matches;
    }
}
?>
