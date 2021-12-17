<?php
namespace Pleraque;

interface QueryOutput
{
    public function getOutput(\PDOStatement $stmt);
}
?>
