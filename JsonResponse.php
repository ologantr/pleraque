<?php
namespace Pleraque;

class JsonResponse extends ResponseWithBody
{
    public static function error(int $code, string $error) : self
    {
        return new self($code, ["status"=> $code,
                                "error" => $error]);
    }

    public static function data(int $code, JsonString $data) : self
    {
        return new self($code, ["status" => $code,
                                "data" => $data->toArray()]);
    }

    protected function setHeaders() : void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    public function return() : void
    {
        echo JsonString::fromArray($this->body);
    }
}
?>
