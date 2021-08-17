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
    }

    private function resetFields() : void
    {
        $this->dataPattern = array_keys($this->dataPattern);
    }

    private function escapeInput() : void
    {
        array_walk_recursive($this->body, function(&$value, $key)
        {
            $value = htmlspecialchars($value);
        });
    }

    private function checkBody() : void
    {
        try
        {
            (new DataMatcher($this->body))->match_with($this->dataPattern);
        }
        catch(\Exception $e)
        {
            $msg = $e->getMessage();
            throw new RestException(StatusCodes::BAD_REQUEST,
                                    "malformed body: $msg");
        }
    }

    public function match(Request $request) : bool
    {
        $this->body = $request->getBody()->toArray();
        return parent::match($request);
    }

    protected function preExecutionChecks() : void
    {
        $this->escapeInput();
        $this->checkBody();
        $this->resetFields();
    }

    protected function getBody()
    {
        return $this->body;
    }

    abstract protected function commandMain() : void;
    abstract protected function buildResponse() : Response;
}
