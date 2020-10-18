<?php namespace EugeneErg\Preparer\SQL\Functions\Traits;

use EugeneErg\Preparer\SQL\Functions\Action;
use EugeneErg\Preparer\SQL\Query\QueryInterface;
use EugeneErg\Preparer\ValueIndexService;
use ReflectionObject;
use ReflectionMethod;

trait FunctionTrait
{
    /**
     * @var FunctionTrait[]
     */
    private array $children = [];
    /**
     * @var Action[]
     */
    private array $actions = [];
    private ValueIndexService $valueIndexService;
    private QueryInterface $query;
    /**
     * @var string[]
     */
    private array $methods = [];

    private function __construct(QueryInterface $query)
    {
        $this->query = $query;
        $this->valueIndexService = new ValueIndexService();
        $methods = (new ReflectionObject($this))->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->hasReturnType()) {
                $methods[$method->getName()] = $method->getReturnType()->getName();
            }
        }
    }

    private function call(string $name, array $arguments = [])
    {
        return $this->getChildren($this->methods[$name], 'call', $name, $arguments);
    }

    private function getChildren(string $class, string $action, string $name, array $arguments = [])
    {
        $index = $this->valueIndexService->getIndex($class,  $action, $name, ...$arguments);

        if (!isset($this->children[$index])) {
            /** @var FunctionTrait $result */
            $result = new $class($this->query);
            $result->actions = $this->actions;
            $result->actions[] = new Action($action, $name, $arguments);
            $this->children[$index] = $result;
        }

        return $this->children[$index];
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
