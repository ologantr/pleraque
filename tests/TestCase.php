<?php
spl_autoload_register(function($class)
{
    require_once '../' . str_replace('Pleraque\\', '', $class) . '.php';
});

use Pleraque as P;

abstract class TestCase
{
    protected function assertEmpty(array $arr) : void
    {
        if(!empty($arr))
            throw new \Exception("assertEmpty failed");
    }

    protected function assertNotEmpty(array $arr) : void
    {
        if(empty($arr))
            throw new \Exception("assertNotEmpty failed");
    }

    protected function assertSame($val1, $val2) : void
    {
        if($val1 !== $val2)
            throw new \Exception("assertSame failed");
    }

    protected function getFileContents(string $filename) : string
    {
        return file_get_contents($filename);
    }

    abstract public function test() : void;
}

final class TestJsonString extends TestCase
{
    public function test() : void
    {
        $this->testBasicDictJson();
        $this->testBasicArrayJson();
        $this->testToArrayJson();
        $this->testJson1();
    }

    private function testBasicDictJson() : void
    {
        $this->assertSame("{\"test\":123}",
                          (string)P\JsonString::fromArray(["test" => 123]));
    }

    private function testBasicArrayJson() : void
    {
        $this->assertSame("[123,456,\"test\"]",
                          (string)P\JsonString::fromArray([123,456,"test"]));
    }

    private function testToArrayJson() : void
    {
        $this->assertSame([1,2,3],
                          P\JsonString::fromString("[1,2,3]")->toArray());
    }

    private function testJson1() : void
    {
        $this->assertSame(P\JsonString::fromString($this->
                                                   getFileContents("Json1.json"))
                          ->toArray(),
                          [
                              ["color" => "red", "value" => "#f00"],
                              ["color" => "green", "value" => "#0f0"],
                              ["color" => "blue", "value" => "#00f"],
                              ["color" => "cyan", "value" => "#0ff"],
                              ["color" => "magenta", "value" => "#f0f"],
                              ["color" => "yellow", "value" => "#ff0"],
                              ["color" => "black", "value" => "#000"],
                          ]);
    }
}

(new TestJsonString())->test();
?>
