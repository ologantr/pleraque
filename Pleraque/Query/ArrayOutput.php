<?php
namespace Pleraque\Query;

class ArrayOutput implements IOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(empty($data) || is_null($data))
            throw new \Exception("empty result");

        return $data;
    }
}
?>
