<?php
// $Id$
namespace Pleraque;

abstract class BodyAttribute
{
    private array $test;

    final public function __construct(array $test)
    {
        $this->test = $test;
    }

    protected function getPattern() : array
    {
        return $this->test;
    }

    abstract public function get() : array;
}
?>
