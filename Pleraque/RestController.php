<?php
namespace Pleraque;
use Pleraque\Utils as U;

class RestController
{
    private static function validateAllRoutes(array $classes) : void
    {
        foreach($classes as $class)
        {
            $r = new \ReflectionClass($class);
            if(!$r->isSubclassOf(RestCommand::class))
                throw new \Exception("Route cannot be applied to "
                                     . $r->getName());
        }
    }

    public static function route() : void
    {
        $allClasses = get_declared_classes();

        try
        {
            $routes = array_filter($allClasses,
                                   fn(string $classname) : bool
                                   =>
                                   count((new \ReflectionClass($classname))
                                         ->getAttributes(Route::class)) == 1);

            self::validateAllRoutes($routes);

            $routes2 = array_map(fn(string $class) : RestCommand
                                 =>
                                 (new \ReflectionClass($class))->newInstance(),
                                 $routes);

            $routes3 = array_filter($routes2,
                                    fn(RestCommand $c) : bool
                                    =>
                                    $c->getRoute()->wasThisRouteRequested());

            $chosen = reset($routes3);

            if($chosen === false || count($routes3) > 1)
                throw new U\RestException(U\StatusCodes::NOT_FOUND,
                                          "route not found");
            $chosen->execute()->return();
        }
        catch(U\RestException $e) { $e->emitError(); }
        catch(\Exception $e)
        {
            (JsonResponse::error(U\StatusCodes::INTERNAL_SERVER_ERROR,
                                 $e->getMessage()))->return();
        }
    }
}
?>
