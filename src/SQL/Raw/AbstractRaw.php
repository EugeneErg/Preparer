<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\Parser\Parser;
use EugeneErg\Preparer\SQL\Query\MainQueryInterface;
use EugeneErg\Preparer\SQL\Query\SubQuery;
use EugeneErg\Preparer\SQL\ValueInterface;

abstract class AbstractRaw
{
    /**
     * @var AbstractTemplate[]
     */
    private array $templates;

    public function __construct(string $query, array $templates = [], string $contextTemplate = null)
    {
        $this->templates = (new Parser($templates, $contextTemplate))->parse($query);
    }

    public function toSubQuery(): SubQuery
    {
        return $this->templatesToSubQuery($this->templates);
    }

    public function toQuery(): MainQueryInterface
    {
        return $this->templatesToQuery($this->templates);
    }

    public function toValue(): ValueInterface
    {
        return $this->templatesToValue($this->templates);
    }

    /**
     * @param AbstractTemplate[] $templates
     * @return SubQuery
     */
    abstract protected function templatesToSubQuery(array $templates): SubQuery;

    /**
     * @param AbstractTemplate[] $templates
     * @return MainQueryInterface
     */
    abstract protected function templatesToQuery(array $templates): MainQueryInterface;

    /**
     * @param AbstractTemplate[] $templates
     * @return ValueInterface
     */
    abstract protected function templatesToValue(array $templates): ValueInterface;
}
