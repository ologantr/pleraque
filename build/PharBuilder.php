<?php
$fileName = "pleraque.phar";
$phar = new Phar($fileName);
$phar->buildFromDirectory("../Pleraque");
$phar->setStub(file_get_contents("Stub.php"));
?>
