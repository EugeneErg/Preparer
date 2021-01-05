<?php namespace EugeneErg\Preparer\Parser;

use EugeneErg\Preparer\ClassCreatorService;

/**
 * Class Parser
 * @package EugeneErg\Preparer
 */
class Parser
{
    private const MATCH_STRING = 0;
    private const MATCH_POSITION = 1;

    /**
     * @var PatternOption[]
     */
    private array $patterns;
    private ?string $contextTemplateClass;
    private ?string $contextTemplatePattern;
    private ClassCreatorService $classCreatorService;

    /**
     * Parser constructor.
     * @param string[]|AbstractTemplate[] $templates
     * @param string|null $contextTemplateClass
     */
    public function __construct(array $templates, string $contextTemplateClass = null)
    {
        foreach ($templates as $itemClass) {
            $this->addPattern($itemClass);
        }

        $this->contextTemplateClass = $contextTemplateClass;

        if ($contextTemplateClass !== null) {
            $this->contextTemplatePattern = $this->quote($contextTemplateClass::TEMPLATE);
        }

        $this->classCreatorService = ClassCreatorService::instance();
    }

    /**
     * @param string $query
     * @return AbstractTemplate[]
     */
    public function parse(string $query): array
    {
        $position = 0;
        $items = array_map(function(...$matches) use(&$position, $query) {
            $items = [];
            unset($matches[0]);

            foreach ($this->patterns as $pattern => $option) {
                $match = array_splice($matches, 0, $option->getCount());

                if ($match[0][self::MATCH_STRING] !== null) {
                    $missed = $match[0][self::MATCH_POSITION] - $position;

                    if ($missed !== 0) {
                        $items = array_merge($items, $this->getContextItems($query, $position, $missed));
                    }

                    $items[] = $this->createItem($match,  $option->getClassName());
                    $position = $match[0][self::MATCH_POSITION] + strlen($match[0][self::MATCH_STRING]);
                }
            }

            return $items;
        }, ...$this->getMatches(
            $query,
            '(' . implode(')|(', array_keys($this->patterns)) . ')'
        ));

        $missed = strlen($query) - $position;

        if ($missed !== 0) {
            $items[] = $this->getContextItems($query, $position, $missed);
        }

        return array_merge(...$items);
    }

    /**
     * @param string $query
     * @param int $position
     * @param int $missed
     * @return AbstractTemplate[]
     */
    private function getContextItems(string $query, int $position, int $missed): array
    {
        if ($this->contextTemplateClass === null) {
            return [];
        }

        return array_map(function(array ...$match): AbstractTemplate {
            return $this->createItem($match, $this->contextTemplateClass);
        }, ...$this->getMatches(substr($query, $position, $missed), $this->contextTemplatePattern));
    }

    /**
     * @param string $query
     * @param string $template
     * @return array
     */
    private function getMatches(string $query, string $template): array
    {
        preg_match_all(
            "/$template/",
            $query,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );

        return $matches;
    }

    /**
     * @param string|AbstractTemplate $className
     */
    private function addPattern(string $className): void
    {
        $pattern = $this->quote($className::TEMPLATE);
        preg_match_all(
            "/$pattern/",
            '',
            $matches
        );
        $this->patterns[$pattern] = new PatternOption(count($matches), $className);
    }

    /**
     * @param string $template
     * @return string
     */
    private function quote(string $template): string
    {
        return str_replace('/', '\/', $template);
    }

    /**
     * @param array $match
     * @param string $itemClass
     * @return AbstractTemplate
     */
    private function createItem(array $match, string $itemClass): AbstractTemplate
    {
        return new $itemClass(...array_column($match, self::MATCH_STRING));
    }
}
