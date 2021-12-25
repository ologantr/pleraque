<?php
namespace Pleraque;

abstract class RestCommand
{
    private Route $route;
    private \ReflectionClass $selfRefl;

    final public function __construct()
    {
        $this->selfRefl = new \ReflectionClass(static::class);
        $attrs = $this->selfRefl->getAttributes(Route::class);
        $this->route = $attrs[0]->newInstance();
    }

    private function execPropertyAttributes() : void
    {
        $props = array_filter($this->selfRefl->getProperties(),
                              fn(\ReflectionProperty $prop) : bool
                              =>
                              count($prop->getAttributes(BodyAttribute::class,
                                                         \ReflectionAttribute
                                                         ::IS_INSTANCEOF))
                              > 0);

        foreach($props as $prop)
        {
            $attrs = $prop->getAttributes(BodyAttribute::class,
                                          \ReflectionAttribute::IS_INSTANCEOF);
            $prop->setAccessible(true);
            $prop->setValue($this, $attrs[0]->newInstance()->get());
            $prop->setAccessible(false);
        }
    }

    final public function getRoute() : Route
    {
        return $this->route;
    }

    final protected function getUrlParameters() : array
    {
        return $this->route->getUrlParameters();
    }

    final public function execute() : Response
    {
        $this->execPropertyAttributes();
        return $this->main();
    }

    abstract protected function main() : Response;
}
?>
