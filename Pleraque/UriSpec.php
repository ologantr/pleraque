<?php
namespace Pleraque;
use Pleraque\Utils as U;

final class UriSpec
{
    private string $uriSpec;
    private array $tokens;
    private array $lexedArray = [];
    private U\Regex $regex;

    private const TOKEN_TYPES = [UriToken::class =>
                                 "#^[A-Za-z0-9\_\-]+$#",

                                 ParameterRegexToken::class =>
                                 "#^\{[A-Za-z0-9\_\-]+\:\ ?.*\}$#",

                                 ParameterToken::class =>
                                 "#^\{[A-Za-z0-9\_\-]+\}$#"];

    public function __construct(string $uri)
    {
        $this->uriSpec = trim($uri, "/");
        $this->tokenize();
        $this->getLexedArray();
        $this->createRegex();
    }

    private function tokenize() : void
    {
        $this->tokens = explode("/", trim($this->uriSpec, "/"));
    }

    private function getLexedArray() : void
    {
        foreach($this->tokens as $token)
        {
            if((new U\Regex(self::TOKEN_TYPES[UriToken::class]))
               ->match($token))
                array_push($this->lexedArray,
                           new UriToken(str_replace(["{", "}"], "", $token)));
            else if((new U\Regex(self::TOKEN_TYPES[ParameterRegexToken::class]))
               ->match($token))
            {
                $reg = "#^\{([A-Za-z0-9\_\-]+)\:(?:[\ ]+)?(.*)\}$#";
                $matches = (new U\Regex($reg))->getMatches($token);
                $parameterName = $matches[0];
                $requestedRegex = $matches[1];

                array_push($this->lexedArray,
                           new ParameterRegexToken($parameterName,
                                                   $requestedRegex));
            }
            else if((new U\Regex(self::TOKEN_TYPES[ParameterToken::class]))
               ->match($token))
                array_push($this->lexedArray,
                           new ParameterToken(str_replace(["{", "}"], "",
                                                          $token)));
            else
                throw new InvalidArgumentException("invalid token $token");
        }
    }

    private function createRegex() : void
    {
        $this->regex = new U\Regex("#^" . implode("/", $this->lexedArray)
                                 . "$#");
    }

    public function matchWith(string $requestUri) : bool
    {
        return $this->regex->match(trim($requestUri, "/"));
    }

    public function getParameters(string $requestUri) : array
    {
        $values = $this->regex->getMatches(trim($requestUri, "/"));

        $keys = array_map(function(ParameterRegexToken $t) : string
                          {
                              return $t->getName();
                          }, array_filter($this->lexedArray,
                                          function(UriToken $t) : bool
                                          {
                                              return $t instanceof
                                                  ParameterRegexToken;
                                          }));

        if(count($values) == 0)
            throw new Exception("no match");

        return array_combine($keys, $values);
    }
}
?>
