<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Collection;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\SQL\Query\AbstractSource;
use EugeneErg\Preparer\SQL\Query\ReturningQuery;
use EugeneErg\Preparer\SQL\ValueInterface;

abstract class AbstractQueryRaw extends AbstractRaw implements ValueInterface
{
    public function toSubQuery(): AbstractSource
    {
        return $this->templatesToSubQuery($this->getTemplates());
    }

    public function toQuery(): ReturningQuery
    {
        return $this->templatesToQuery($this->getTemplates());
    }

    public function toValue(?string $type = null): ValueInterface
    {
        return $this->templatesToValue($this->getTemplates(), $type);
    }

    /**
     * @param AbstractTemplate[]|Collection $templates
     * @return AbstractSource
     */
    abstract protected function templatesToSubQuery(Collection $templates): AbstractSource;

    /**
     * @param AbstractTemplate[]|Collection $templates
     * @return ReturningQuery
     */
    abstract protected function templatesToQuery(Collection $templates): ReturningQuery;

    /**
     * @param AbstractTemplate[]|Collection $templates
     * @param string|null $type
     * @return ValueInterface
     */
    abstract protected function templatesToValue(Collection $templates, ?string $type): ValueInterface;
}
