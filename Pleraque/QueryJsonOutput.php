<?php
namespace Pleraque;

class QueryJsonOutput implements QueryOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(!is_array($res) || !$res || is_null(reset($res)) ||
           empty($res))
            throw new \Exception("empty result");
        return JsonString::fromArray($res);
    }
}
?>
