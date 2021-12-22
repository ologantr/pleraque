<?php
namespace Pleraque\Utils;
use Pleraque as P;

class RestException extends \Exception
{
    private $response;

    public function __construct(int $code, string $message)
    {
        parent::__construct($message);
        $this->response = P\JsonResponse::error($code, $message);
    }

    public function emitError()
    {
        $this->response->return();
    }
}
?>
