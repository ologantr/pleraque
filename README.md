# Pleraque

## What is it?
Pleraque is a minimalistic PHP library that lets you write a
RESTful API easily, without worrying about tons of dependencies.

It comes with a Database class to manage your database connection and to 
execute your queries, and a main REST class to manage your routes.

## How to use it?
This library right now is not ready for Composer, so for the time
being you'll have to checkout this repo and use it.

Maybe I'll put it on Composer for general availability in the near future.

## Conclusions
Feel free to send me any concerns and write a ticket here if you find a problem/bug.

I'm working on an API documentation right now.

## Working example (as of current trunk)
Note that API and results are subject to change as this is a work in progress.

```
use Pleraque as P;

$controller = new P\RestController();

$c->addGetCommand(new class("test")
                  extends P\RestCommand
{
    protected function preExecutionChecks() : void
    {

    }

    protected function commandMain() : void
    {

    }

    protected function buildResponse() : P\Response
    {
        return P\JsonResponse::data(P\StatusCodes::OK,
                                    P\JsonString::fromArray(["res" => 1]));
    }
});

$c->addGetCommand(new class("test/{id}")
                  extends P\RestCommand
{
    protected function preExecutionChecks() : void
    {

    }

    protected function commandMain() : void
    {

    }

    protected function buildResponse() : P\Response
    {
        $m = $this->getParameters();
        return P\JsonResponse::data(P\StatusCodes::OK,
                                    P\JsonString::fromArray(["id" => $m["id"]]));
    }
});

$c->addGetCommand(new class("user/{name:[a-z]+}")
                  extends P\RestCommand
{
    protected function preExecutionChecks() : void
    {

    }

    protected function commandMain() : void
    {

    }

    protected function buildResponse() : P\Response
    {
        $m = $this->getParameters();
        return P\JsonResponse::data(P\StatusCodes::OK,
                                    P\JsonString::fromArray(["name" => $m["name"]]));
    }
});
```

Result requesting GET /test:
```
{"status": 200, "data": {"res": "123"}}
```
Result requesting GET /test/321:
```
{"status": 200, "data": {"id": "321"}}
```
Result requesting GET /user/test:
```
{"status": 200, "data": {"name": "test"}}
```
Result requesting GET /user/Test:
```
{"status":404,"error":"route not found"}
```
