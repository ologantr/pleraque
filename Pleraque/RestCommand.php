<?php
namespace Pleraque;

abstract class RestCommand
{
    private UriSpec $uriSpec;
    private string $url;

    public function __construct(string $uriSpec)
    {
        $this->url = Request::getInstance()->getUrl();
        $this->uriSpec = new UriSpec($uriSpec);
    }

    public function match() : bool
    {
        return $this->uriSpec->matchWith($this->url);
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

    final protected function getParameters() : array
    {
        return $this->uriSpec->getParameters($this->url);
    }
}
?>
