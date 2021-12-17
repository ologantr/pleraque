<?php
namespace Pleraque;

class QueryBooleanOutput implements QueryOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $stmt->bindColumn(1, $res, \PDO::PARAM_BOOL);
        $data = $stmt->fetch(\PDO::FETCH_BOUND);
        return $res;
    }
}
?>
