<?php
namespace Pleraque;

class QueryStringOutput implements QueryOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $res = $stmt->fetch(\PDO::FETCH_NUM);
        if(!$res) throw new \Exception("empty result");
        return reset($res);
    }
}
?>
