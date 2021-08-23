<?php
namespace Pleraque;

abstract class RestCommand
{
    private $regex;
    private $url;

    public function __construct(Regex $regex)
    {
        $this->regex = $regex;
        $this->url = Request::getInstance()->getUrl();
    }

    public function match() : bool
    {
        return $this->regex->match($this->url);
    }

    abstract protected function preExecutionChecks() : void;
    abstract protected function commandMain() : void;
    abstract protected function buildResponse() : Response;

    final public function execute() : Response
    {
        $this->preExecutionChecks();
        $this->commandMain();
        return $this->buildResponse();
    }

    final protected function getUrlMatches() : array
    {
        return $this->regex->getMatches($this->url);
    }
}
?>
