<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Collection;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\Parser\Parser;

abstract class AbstractRaw
{
    /**
     * @var AbstractTemplate[]|Collection
     */
    private Collection $templates;

    public function __construct(string $query, array $templates = [], string $contextTemplate = null)
    {
        $this->templates = new Collection((new Parser($templates, $contextTemplate))->parse($query));
    }

    /**
     * @return AbstractTemplate[]|Collection
     */
    protected function getTemplates(): Collection
    {
        return $this->templates;
    }
}
