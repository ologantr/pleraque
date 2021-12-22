<?php
namespace Pleraque;

class Database
{
    private static ?self $instance = null;
    private \PDO $dbconn;

    private function __clone() {}
    private function __wakeup() {}

    private function __construct(string $pdoString, string $uname,
                                 string $pwd)
    {
        $this->dbconn = new \PDO($pdoString, $uname, $pwd);
        $this->dbconn->setAttribute(\PDO::ATTR_ERRMODE,
                                    \PDO::ERRMODE_EXCEPTION);
    }

    public static function connect(string $pdoString, string $uname = null,
                                   string $pwd = null) : void
    {
        if(self::$instance == null)
            self::$instance = new static($pdoString, $uname, $pwd);
        else
            throw new \Exception("Database::connect must be called once");
    }

    public function lastInsertId(?string $name = null)
    {
        return $this->dbconn->lastInsertId($name);
    }

    public function getQuery(string $query, array $arr = null) : Query
    {
        return new class($query, $this->dbconn, $arr) extends Query
        {
            private $dbconn;

            public function __construct(string $query, \PDO $dbconn,
                                        array $arr = null)
            {
                parent::__construct($query, $arr);
                $this->dbconn = $dbconn;
            }

            protected function setStatement() : void
            {
                $this->stmt = $this->dbconn->prepare($this->query);
            }
        };
    }

    public static function getInstance() : self
    {
        if(self::$instance == null)
            throw new \Exception("Database::connect must be called first");
        return self::$instance;
    }

    public function startTransaction() : void
    {
        $this->dbconn->beginTransaction();
    }

    public function commit() : void
    {
        $this->dbconn->commit();
    }

    public function rollback() : void
    {
        $this->dbconn->rollBack();
    }

    public function withTransaction(Query ...$queries) : void
    {
        $this->startTransaction();

        foreach($queries as $q)
        {
            if(!$q->execute())
            {
                $this->rollback();
                $msg = $q->error();
                throw new \Exception("SQL Error: $msg");
            }
        }
        $this->commit();
    }
}
?>
