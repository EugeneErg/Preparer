<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\Parser\Parser;

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

    public function toSubQuery(): Container
    {
        return $this->templatesToSubQuery($this->templates);
    }

    public function toQuery(): Container
    {
        return $this->templatesToQuery($this->templates);
    }

    public function toValue(): Container
    {
        return $this->templatesToValue($this->templates);
    }

    /**
     * @param AbstractTemplate[] $templates
     * @return Container
     */
    abstract protected function templatesToSubQuery(array $templates): Container;

    /**
     * @param AbstractTemplate[] $templates
     * @return Container
     */
    abstract protected function templatesToQuery(array $templates): Container;

    /**
     * @param AbstractTemplate[] $templates
     * @return Container
     */
    abstract protected function templatesToValue(array $templates): Container;
}
