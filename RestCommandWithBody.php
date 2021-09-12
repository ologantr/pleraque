<?php
namespace Pleraque;

abstract class RestCommandWithBody extends RestCommand
{
    private $dataPattern;
    private $body;

    public function __construct(Regex $regex, array $dataPattern)
    {
        parent::__construct($regex);
        $this->dataPattern = $dataPattern;
        $this->body = Request::getInstance()->getBody()->toArray();
    }

    private function escapeInput() : void
    {
        array_walk_recursive($this->body, function(&$value, $key)
        {
            if(!is_null($value))
                $value = htmlspecialchars($value);
        });
    }

    private function checkBody() : void
    {
        try
        {
            (new DataMatcher($this->dataPattern))->matchWith($this->body);
        }
        catch(\Exception $e)
        {
            $msg = $e->getMessage();
            throw new RestException(StatusCodes::BAD_REQUEST,
                                    "malformed body: $msg");
        }
    }

    protected function preExecutionChecks() : void
    {
        $this->escapeInput();
        $this->checkBody();
    }

    protected function getBody()
    {
        return $this->body;
    }

    abstract protected function commandMain() : void;
    abstract protected function buildResponse() : Response;
}
