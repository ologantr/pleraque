<?php
namespace Pleraque;

#[\Attribute(\ATTRIBUTE::TARGET_CLASS)]
final class Route
{
    private static string $url;
    private string $method;
    private UriSpec $uriDesc;

    public function __construct(string $method,
                                string $uriDesc)
    {
        $this->uriDesc = new UriSpec($uriDesc);
        $this->methods = $method;
        self::$url = Request::getInstance()->getUrl();
    }

    final public function wasThisRouteRequested() : bool
    {
        return Request::getInstance()->getMethod()
            == $this->methods &&
            $this->uriDesc->matchWith(self::$url);
    }

    final public function getUrlParameters() : array
    {
        return $this->uriDesc->getParameters(self::$url);
    }
}
?>
