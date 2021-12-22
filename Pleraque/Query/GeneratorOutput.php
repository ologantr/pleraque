<?php
namespace Pleraque\Query;

class GeneratorOutput implements IOutput
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
