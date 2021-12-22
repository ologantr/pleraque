<?php
namespace Pleraque\Utils;

class DataMatcher
{
    private array $dictPattern;
    private array $fnDict;
    private string $dateFormat;

    public function __construct(array $dictPattern)
    {
        $this->dictPattern = $dictPattern;
        $this->buildFnDict();
        $this->checkDictPattern();
    }

    private function checkDictPattern() : void
    {
        if(!$this->isDictionary($this->dictPattern))
            throw new \InvalidArgumentException("invalid pattern dictionary");

        foreach($this->dictPattern as $name => $type)
            if(!isset($this->fnDict["$type"]))
                throw new \InvalidArgumentException("invalid pattern dictionary"
                                                    . " - unknown type $type");
    }

    private function buildFnDict()
    {
        $this->fnDict = [
            "#string#" => function($toCheck) : bool
            {
                return is_string($toCheck);
            },
            "#?string#" => function($toCheck) : bool
            {
                return is_string($toCheck) || is_null($toCheck);
            },
            "#string_notempty#" => function($toCheck) : bool
            {
                return is_string($toCheck) && strlen($toCheck) !== 0;
            },
            "#int#" => function($toCheck) : bool
            {
                return is_int($toCheck);
            },
            "#dict#" => function($toCheck) : bool
            {
                return is_array($toCheck) &&
                    $this->isDictionary($toCheck);
            },
            "#array#" => function($toCheck) : bool
            {
                return is_array($toCheck) &&
                    !$this->isDictionary($toCheck);
            },
            "#date#" => function($toCheck) : bool
            {
                if(!is_string($toCheck)) return false;

                $this->checkDateWithFormat($toCheck);
                return true;
            }];
    }

    private function isDictionary(array $arr) : bool
    {
        $countOrig = count($arr);
        $countFilter = count(array_filter(array_keys($arr),
                                          function($elem) : bool
                                          {
                                              return is_string($elem);
                                          }));

        if($arr == [] || $countFilter == 0 || $countFilter < $countOrig)
            return false;
        else if($countOrig == $countFilter)
            return true;

        return false;
    }

    private function getDateFormatFromType(string $type) : string
    {
        $matches = (new Regex("#^\#date\#(.*)$#"))->getMatches($type);
        return count($matches) == 0 || strlen($matches[0]) == 0 ?
                               "Y-m-d" : $matches[0];
    }

    private function checkDateWithFormat(string $date) : void
    {
        \DateTimeImmutable::createFromFormat($this->dateFormat, $date);
        $errors = \DateTimeImmutable::getLastErrors();

        if($errors["error_count"] !== 0 || $errors["warning_count"] !== 0)
            throw new \Exception("Date Error: " .
                                 array_merge($errors["errors"],
                                             $errors["warnings"])[0]);
    }

    private function matchType(string $type, $toCheck) : void
    {
        if((new Regex("#^\#date\#.*$#"))->match($type))
        {
            $this->dateFormat = $this->getDateFormatFromType($type);
            $type = "#date#";
        }

        if(!($this->fnDict["$type"])($toCheck))
            throw new \Exception("type mismatch on $toCheck - expected $type");
    }

    private function matchDict(array $data) : void
    {
        foreach($this->dictPattern as $key => $value)
        {
            if(array_key_exists($key, $data))
                $this->matchType($value, $data[$key]);
            else
                throw new \Exception("missing field: $key");
        }
    }

    private function checkDataCount(array $data) : void
    {
        if(count($data) !== count($this->dictPattern))
           throw new \Exception("count mismatch");
    }

    public function matchWith(array $data) : bool
    {
        $this->checkDataCount($data);
        $this->matchDict($data);
        return true;
    }
}
?>
