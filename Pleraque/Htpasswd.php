<?php
namespace Pleraque;
use Pleraque\Utils as U;

final class Htpasswd
{
    private static U\Regex $entryRegex;
    private string $filePath;
    private array $map = [];
    private bool $doesFileExist;

    public function __construct(string $filePath)
    {
        self::$entryRegex = new U\Regex("#^(.*): (.*)$#");
        $this->filePath = $filePath;
        $this->doesFileExist = file_exists($this->filePath);
        $this->buildMapFromFile();
    }

    private function buildMapFromFile() : void
    {
        if(!$this->doesFileExist)
            return;

        $file = array_map(fn(string $entry) : string => rtrim($entry),
                          file($this->filePath));

        $entryToDict = function(string $entry) : array
        {
            $arr = self::$entryRegex->getMatches($entry);

            if(count($arr) == 0)
                throw new \Exception("invalid htpasswd file");

            return [$arr[0] => U\Password::fromHash($arr[1])];
        };

        $this->map = array_merge(...array_map($entryToDict, $file));
    }

    public function addEntry(string $username, string $plain) : void
    {
        $this->map["$username"] = U\Password::fromPlaintext($plain);
    }

    public function removeEntry(string $username) : void
    {
        unset($this->map["$username"]);
    }

    public function verifyUserPwd(string $username, string $plainPwd) : bool
    {
        return $this->map["$username"]->matchWithPlaintext($plainPwd);
    }

    public function commit() : void
    {
        $keyValueToEntry = fn(string $uname, string $hash)
                         => $uname . ": " . $hash . "\n";
        $arr = [];

        foreach($this->map as $k => $v)
            array_push($arr, $keyValueToEntry($k, $v));

        file_put_contents($this->filePath, $arr, LOCK_EX);
    }
}
?>
