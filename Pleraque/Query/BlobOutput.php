<?php
namespace Pleraque\Query;

final class BlobOutput implements IOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        $stmt->bindColumn(1, $blob, \PDO::PARAM_LOB);
        $stmt->fetch(\PDO::FETCH_BOUND);
        return $blob;
    }
}
?>
