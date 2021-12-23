<?php
namespace Pleraque;
use Pleraque\Utils as U;

final class Request
{
    private static ?self $instance = null;
    private static IURLRetrieverFunction $urlRetrieverFn;
    private string $reqUrl;
    private string $method;
    private U\JsonString $body;
    private array $headers;

    private function __clone() {}
    public function __wakeup() {}

    private function __construct()
    {
        $this->setUrl();
        $this->setMethod();
        $this->setBody();
        $this->setHeaders();
    }

    public static function getInstance() : self
    {
        if(self::$instance == null) self::$instance = new static();
        return self::$instance;
    }

    public static function setUrlRetrieverFunction(IUrlRetrieverFunction $fn)
        : void
    {
        self::$urlRetrieverFn = $fn;
    }

    private function setUrl() : void
    {
        $this->reqUrl = isset(self::$urlRetrieverFn) ?
                      self::$urlRetrieverFn->getUrl() :
                      parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    private function setMethod() : void
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    private function setBody() : void
    {
        $body = file_get_contents("php://input");

        if(!empty($body))
            $this->body = U\JsonString::fromString($body);
        else
            $this->body = U\JsonString::fromArray([]);
    }

    private function setHeaders() : void
    {
        $regex = new U\Regex("#^HTTP_*#");

        $h = fn(string $key) : string =>
           implode("-", array_map(fn(string $word) : string =>
                                  ucwords(strtolower($word)),
                                  explode("_", str_replace("HTTP_",
                                                           "",
                                                           $key))));

        $filtered_server = array_filter($_SERVER,
                                        fn(string $k) : bool =>
                                        $regex->match($k),
                                        ARRAY_FILTER_USE_KEY);

        $this->headers = array_combine(array_map($h,
                                                 array_keys($filtered_server)),
                                       array_values($filtered_server));
    }

    public function getUrl() : string
    {
        return $this->reqUrl;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getBody() : U\JsonString
    {
        return $this->body;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }
}
?>
