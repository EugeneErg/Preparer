<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\Parser\Parser;
use EugeneErg\Preparer\SQL\Query\AbstractQuery;
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

    public function toQuery(): AbstractQuery
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
     * @return AbstractQuery
     */
    abstract protected function templatesToQuery(array $templates): AbstractQuery;

    /**
     * @param AbstractTemplate[] $templates
     * @return ValueInterface
     */
    abstract protected function templatesToValue(array $templates): ValueInterface;
}
