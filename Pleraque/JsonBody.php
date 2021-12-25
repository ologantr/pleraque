<?php
namespace Pleraque;
use Pleraque\Utils as U;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class JsonBody extends BodyAttribute
{
    private array $body;

    private function checkBody() : void
    {
        try
        {
            (new U\DataMatcher($this->getPattern()))
                ->matchWith($this->body);
        }
        catch(\Exception $e)
        {
            throw new U\RestException(U\StatusCodes::BAD_REQUEST,
                                      "malformed body: {$e->getMessage()}");
        }
    }

    private function escapeInput() : void
    {
        array_walk_recursive($this->body, function(&$value, $key)
        {
            if(!is_null($value))
                $value = htmlspecialchars($value);
        });
    }

    public function get() : array
    {
        $this->body = U\JsonString::fromString(Request::getInstance()
                                               ->getRawBody())->toArray();
        $this->escapeInput();
        $this->checkBody();

        return $this->body;
    }
}
?>
