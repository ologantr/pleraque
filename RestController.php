<?php
namespace Pleraque;

class RestController
{
    private $request;
    private $commandDict = [];

    public function __construct()
    {
        $this->request = new Request();
    }

    private function addCommandWithMethod(string $method,
                                          RestCommand $command) : void
    {
        if(!array_key_exists($method, $this->commandDict))
            $this->commandDict[$method] = [];

        array_push($this->commandDict[$method], $command);
    }

    public function addGetCommand(RestCommand $command) : void
    {
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
            foreach($this->commandDict as $method => $dict)
            {
                if($this->request->getMethod() == $method)
                {
                    foreach($dict as $route)
                        if($route->match($this->request))
                        {
                            $route->execute()->return();
                            exit();
                        }
                }
            }

            throw new RestException(StatusCodes::NOT_FOUND, "route not found");
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
