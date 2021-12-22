<?php
namespace Pleraque\Query;

class BooleanOutput implements IOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $stmt->bindColumn(1, $res, \PDO::PARAM_BOOL);
        $data = $stmt->fetch(\PDO::FETCH_BOUND);
        return $res;
    }
}
?>
