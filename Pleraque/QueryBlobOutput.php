<?php
namespace Pleraque;

final class QueryBlobOutput implements QueryOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $stmt->bindColumn(1, $blob, \PDO::PARAM_LOB);
        $stmt->fetch(\PDO::FETCH_BOUND);
        return $blob;
    }
}
?>
