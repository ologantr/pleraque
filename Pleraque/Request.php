<?php
namespace Pleraque;

final class Request
{
    private static ?self $instance = null;
    private static IURLRetrieverFunction $urlRetrieverFn;
    private string $reqUrl;
    private string $method;
    private JsonString $body;
    private array $headers;

    private function __clone() {}
    private function __wakeup() {}

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
            $this->body = JsonString::fromString($body);
        else
            $this->body = JsonString::fromArray([]);
    }

    private function setHeaders() : void
    {
        $regex = new Regex("#^HTTP_*#");

        $h = function(string $key) : string
        {
            return implode("-", array_map(function(string $word) : string
                                          {
                                              return ucwords(strtolower($word));
                                          }, explode("_", str_replace("HTTP_",
                                                                      "",
                                                                      $key))));
        };

        $filtered_server = array_filter($_SERVER,
                                        function(string $k) use ($regex): bool
                                        {
                                            return $regex->match($k);
                                        }, ARRAY_FILTER_USE_KEY);

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

    public function getBody() : JsonString
    {
        return $this->body;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }
}
?>
