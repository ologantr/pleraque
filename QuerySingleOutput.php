<?php
namespace Pleraque;

class QuerySingleOutput implements QueryOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!$data || empty($data))
            throw new \Exception("empty result");

        return $data;
    }
}
?>
