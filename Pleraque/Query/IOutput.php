<?php
namespace Pleraque;

interface IOutput
{
    public function getOutput(\PDOStatement $stmt);
}
?>
