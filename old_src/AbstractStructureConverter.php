<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Parser\Parser;
use EugeneErg\Preparer\Record\StructureListRecord;
use ReflectionException;

abstract class AbstractStructureConverter
{
    private array $templates;
    private ?string $contextTemplate;
    private Parser $parser;

    /**
     * AbstractStructureConverter constructor.
     * @param array $templates
     * @param string|null $contextTemplate
     * @throws ReflectionException
     */
    public function __construct(array $templates, string $contextTemplate = null)
    {
        $this->templates = $templates;
        $this->contextTemplate = $contextTemplate;
        /** @var Parser $parser */
        $parser = ClassCreatorService::instance()->createSingle(Parser::class, [
            $this->templates,
            $this->contextTemplate
        ]);
        $this->parser = $parser;
    }

    /**
     * @param string $query
     * @return StructureListRecord
     */
    public function fromString(string $query): StructureListRecord
    {
        return $this->templatesToStructure($this->parser->parse($query));
    }

    /**
     * @param StructureListRecord $structureListRecord
     * @return string
     */
    abstract public function toString(StructureListRecord $structureListRecord): string;

    /**
     * @param array $templates
     * @return StructureListRecord
     */
    abstract protected function templatesToStructure(array $templates): StructureListRecord;
}