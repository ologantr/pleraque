<?php
namespace Pleraque;

class RestController
{
    private $requestMethod;
    private $commands = [];

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
            $this->addCommandWithMethod(HttpMethods::GET, $command);
    }

    public function addPostCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(HttpMethods::POST, $command);
    }

    public function addPutCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(HttpMethods::PUT, $command);
    }

    public function addPatchCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(HttpMethods::PATCH, $command);
    }

    public function addDeleteCommand(RestCommand $command) : void
    {
        $this->addCommandWithMethod(HttpMethods::DELETE, $command);
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

            throw new RestException(StatusCodes::NOT_FOUND,
                                    "route not found");
        }
        catch(RestException $e)
        {
            $e->emitError();
        }
        catch(\Exception $e)
        {
            (JsonResponse::error(StatusCodes::INTERNAL_SERVER_ERROR,
                                 $e->getMessage()))->return();
        }
    }
}
?>