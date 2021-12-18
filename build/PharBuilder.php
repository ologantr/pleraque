<?php
$fileName = "pleraque.phar";
$phar = new Phar($fileName);

$stub = <<<EOSTUB
<?php
spl_autoload_register(function(string \$class) : void {
	require "phar://pleraque.phar/" . 
		str_replace("\\\\", "/",
			str_replace("Pleraque\\\\", "", \$class))
			. ".php";
});
Phar::mapPhar("pleraque.phar");
__HALT_COMPILER();
EOSTUB;

$phar->buildFromDirectory("../Pleraque");
$phar->setStub($stub);
?>
