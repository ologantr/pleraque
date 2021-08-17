<?php
namespace Pleraque;

class Request
{
    private $reqUrl;
    private $method;
    private $body;

    public function __construct()
    {
        $this->setUrl();
        $this->setMethod();
        $this->setBody();
    }

    private function setUrl() : void
    {
        $this->reqUrl = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    private function setMethod() : void
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    private function setBody() : void
    {
        $body = file_get_contents("php://input");

        if(!empty($body))
            $this->body = JsonString::fromString($body);
        else
            $this->body = JsonString::fromArray([]);
    }

    public function getUrl() : string
    {
        return $this->reqUrl;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getBody() : JsonString
    {
        return $this->body;
    }
}
?>
