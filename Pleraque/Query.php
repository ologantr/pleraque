<?php
namespace Pleraque;

abstract class Query implements \Stringable
{
    protected string $query;
    protected \PDOStatement $stmt;
    private array $args;

    public function __construct(string $q, array $arr = null)
    {
        $this->query = $q;
        $this->args = $arr;
    }

    public function __toString()
    {
        return $this->query;
    }

    abstract protected function setStatement() : void;

    public function execute() : bool
    {
        $this->setStatement();
        return $this->stmt->execute($this->args);
    }

    public function substitute(string $key, string $value) : void
    {
        $this->query = str_replace("%[$key]", $value, $this->query);
    }

    public function getResult(QueryOutput $out)
    {
        $this->execute();
        return $out->getOutput($this->stmt);
    }

    public function error() : array
    {
        return $this->stmt->errorInfo();
    }
}
?>
