<?php
namespace Pleraque;

class DataMatcher
{
    private $data;
    private $check;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function is_dictionary(array $arr) : bool
    {
        if($arr == []) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function match_type(string $type, $to_check) : bool
    {
        $res = null;
        $arr = is_array($to_check);

        switch($type)
        {
            case "#string#":
                $res = is_string($to_check);
                break;
            case "#?string#":
                $res = is_string($to_check) || is_null($to_check);
                break;
            case "#string_notempty#":
                $res = is_string($to_check) && strlen($to_check) !== 0;
                break;
            case "#int#":
                var_dump($to_check);
                $res = is_int($to_check);
                break;
            case "#dict#":
                $res = $arr && $this->is_dictionary($to_check);
                break;
            case "#array#":
                $res = $arr && !$this->is_dictionary($to_check);
                break;
            case "#date#":
                $res = is_string($to_check) && $this->is_date($to_check);
                break;
            default:
                throw new \Exception("unknown type");
        }

        if(!$res)
        {
            $t = str_replace("#", "", $type);
            throw new RestException(StatusCodes::BAD_REQUEST, "type mismatch " .
                                    "on $to_check, expected $t");
        }

        return true;
    }

    private function is_date(string $date) : bool
    {
        $year_first_regex = new Regex("#^\d{4}-\d{2}-\d{2}$#");
        $year_last_regex = new Regex("#^\d{2}-\d{2}-\d{4}$#");

        $year_first_match = $year_first_regex->match($date);
        $year_last_match = $year_last_regex->match($date);

        if($year_first_match) $year_first = true;
        else if($year_last_match) $year_first = false;
        else
            throw new RestException(StatusCodes::BAD_REQUEST,
                                    "invalid date format");

        $arr = explode('-', $date);

        if($year_first)
        {
            if(!checkdate($arr[1], $arr[2], $arr[0]))
                throw new RestException(StatusCodes::BAD_REQUEST,
                                        "invalid date - year first");
        }
        else
            if(!checkdate($arr[1], $arr[0], $arr[2]))
                throw new RestException(StatusCodes::BAD_REQUEST,
                                        "invalid date - year last");

        return true;
    }

    private function match_dict() : void
    {
        foreach($this->check as $key => $value)
        {
            if(!empty($this->data[$key]) ||
               is_null($this->data[$key]))
                $this->match_type($value, $this->data[$key]);
            else
                throw new RestException(StatusCodes::BAD_REQUEST,
                                        "missing field: $key");
        }
    }

    private function match_array() : void
    {
        for($i = 0; $i < count($this->data); ++$i)
            $this->match_type($this->check[$i], $this->data[$i]);
    }

    public function match_with(array $check) : void
    {
        $this->check = $check;

        if(count($this->data) !== count($this->check))
            throw new RestException(StatusCodes::BAD_REQUEST,
                                    "count mismatch");

        if($this->is_dictionary($this->data))
            $this->match_dict($this->check);
        else
            $this->match_array($this->check);
    }
}
?>
