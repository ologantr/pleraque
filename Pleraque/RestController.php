<?php
namespace Pleraque;
use Pleraque\Utils as U;

class RestController
{
    private string $requestMethod;
    private array $commands = [];

    public function __construct()
    {
        $this->requestMethod = Request::getInstance()->getMethod();
    }

    private function addCommandWithMethod(string $method,
                                          RestCommand $command) : void
    {
        if($method == $this->requestMethod)
            array_push($this->commands, $command);
    }

    public function addGetCommand(RestCommand $command) : void
    {
        if($command instanceof RestCommandWithBody)
            throw new \InvalidArgumentException("GET doesn't allow " .
                                                "requests with a body");
        else
            $this->addCommandWithMethod(U\HttpMethods::GET, $command);
    }

    public function addPostCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(U\HttpMethods::POST, $command);
    }

    public function addPutCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(U\HttpMethods::PUT, $command);
    }

    public function addPatchCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(U\HttpMethods::PATCH, $command);
    }

    public function addDeleteCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(U\HttpMethods::DELETE, $command);
    }

    public function route()
    {
        try
        {
            foreach($this->commands as $route)
            {
                if($route->match())
                {
                    $route->execute()->return();
                    exit();
                }
            }

            throw new U\RestException(U\StatusCodes::NOT_FOUND,
                                    "route not found");
        }
        catch(U\RestException $e)
        {
            $e->emitError();
        }
        catch(\Exception $e)
        {
            (JsonResponse::error(U\StatusCodes::INTERNAL_SERVER_ERROR,
                                 $e->getMessage()))->return();
        }
    }
}
?>
