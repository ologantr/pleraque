<?php
namespace Pleraque\Utils;

final class XML implements \Stringable
{
    private $rootNodeName;
    private $xml;

    private function __construct(string $xml)
    {
        if(($this->xml = @simplexml_load_string($xml))
           === false)
            throw new \InvalidArgumentException("malformed xml: "
                                                . $xml);
        $this->rootNodeName = $this->xml->getName();
    }

    private static function arrayToXML(array $arr,
                                       \SimpleXMLElement &$elem)
        : void
    {
        foreach($arr as $k => $v)
        {
            if(is_array($v))
            {
                if(!is_numeric($k))
                {
                    $sub = $elem->addChild($k);
                    $this->arrayToXML($v, $sub);
                }
                else
                {
                    $this->arrayToXML($v, $sub);
                }
            }
            else
                $elem->addChild($k, $v);
        }
    }

    public static function fromString(string $xml) : self
    {
        return new self($xml);
    }

    public static function fromArray(string $rootNode,
                                     array $arr) : self
    {
        $root = new \SimpleXMLElement("<" . $rootNode . "/>");
        self::arrayToXML($arr, $root);

        return new self($root->asXML());
    }

    public function __toString()
    {
        return $this->xml->asXML();
    }
}
?>
