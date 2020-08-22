<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Parser\Parser;
use EugeneErg\Preparer\Record\StructureListRecord;
use ReflectionException;

abstract class AbstractStructureConverter
{
    protected array $templates = [];
    protected ?string $contextTemplate = null;
    private Parser $parser;

    /**
     * AbstractStructureConverter constructor.
     * @throws ReflectionException
     */
    public function __construct()
    {
        $this->parser = ClassCreatorService::instance()->createSingle(Parser::class, [
            $this->templates,
            $this->contextTemplate
        ]);
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