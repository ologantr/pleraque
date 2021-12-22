<?php
namespace Pleraque;

final class JsonString implements \Stringable
{
    private string $json;

    private function __construct(string $json)
    {
        $this->json = $json;

        @json_decode($this->json);
        if(json_last_error() !== JSON_ERROR_NONE)
            throw new \InvalidArgumentException("malformed json: " . $this->json);
    }

    public static function fromArray(array $arr) : self
    {
        return new self(json_encode($arr, JSON_UNESCAPED_UNICODE));
    }

    public static function fromString(string $json) : self
    {
        return new self($json);
    }

    public function __toString() : string
    {
        return $this->json;
    }

    public function toArray() : array
    {
        return json_decode($this->json, true);
    }

    public function merge(self $to_merge) : self
    {
        $arr = array_merge($this->toArray(), $to_merge->toArray());
        return self::fromArray($arr);
    }
}
?>
