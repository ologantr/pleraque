<?php
// $Id$
namespace Pleraque;

class QueryGeneratorOutput implements QueryOutput
{
    public function getOutput(\PDOStatement $stmt)
    {
        return (function() use ($stmt)
        {
            while($row = $stmt->fetch(\PDO::FETCH_ASSOC))
                yield $row;
        })();
    }
}
?>
