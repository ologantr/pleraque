<?php
namespace Pleraque;

class RestException extends \Exception
{
    private $response;

    public function __construct(int $code, string $message)
    {
        parent::__construct($message);
        $this->response = JsonResponse::error($code, $message);
    }

    public function emitError()
    {
        $this->response->return();
    }
}
?>
