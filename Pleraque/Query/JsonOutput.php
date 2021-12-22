<?php
namespace Pleraque\Query;
use Pleraque\Utils as U;

class JsonOutput implements IOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(!is_array($res) || !$res || is_null(reset($res)) ||
           empty($res))
            throw new \Exception("empty result");
        return U\JsonString::fromArray($res);
    }
}
?>
