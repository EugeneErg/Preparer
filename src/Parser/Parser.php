<?php namespace EugeneErg\Preparer\Parser;

use EugeneErg\Preparer\ClassCreatorService;
use ReflectionException;

/**
 * Class Parser
 * @package EugeneErg\Preparer
 */
class Parser
{
    private const MATCH_STRING = 0;
    private const MATCH_POSITION = 1;

    /**
     * @var string[]
     */
    private $patterns;

    /**
     * @var string
     */
    private $contextTemplateClass;

    /**
     * @var string
     */
    private $contextTemplatePattern;

    /**
     * @var ClassCreatorService
     */
    private $classCreatorService;

    /**
     * Parser constructor.
     * @param string[] $templates
     * @param string $contextTemplateClass
     */
    public function __construct(array $templates, string $contextTemplateClass = ContextTemplate::class)
    {
        foreach ($templates as $pattern => $itemClass) {
            $this->patterns[$this->quote($pattern)] = $itemClass;
        }

        $this->contextTemplateClass = $contextTemplateClass;
        $this->contextTemplatePattern = $this->quote($contextTemplateClass::TEMPLATE);
        $this->classCreatorService = ClassCreatorService::instance();
    }

    /**
     * @param string $query
     * @return AbstractTemplate[]
     */
    public function parse(string $query): array
    {
        $callbacks = [];
        $items = [];
        $position = 0;

        foreach ($this->patterns as $template => $class) {
            $callbacks[$template] = function(array $match) use($class, &$items, &$position, $query) {
                $missed = $match[0][self::MATCH_POSITION] - $position;

                if ($missed !== 0) {
                    preg_match_all(
                        $this->contextTemplatePattern,
                        substr($query, $position, $missed),
                        $matches,
                        PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
                    );

                    foreach ($matches as $contextMatch) {
                        $items[] = $this->createItem($contextMatch, $this->contextTemplateClass);
                    }
                }

                $items[] = $this->createItem($match, $class);
                $position = $match[0][self::MATCH_POSITION] + strlen($match[0][self::MATCH_STRING]);
            };
        }

        preg_replace_callback_array(
            $callbacks,
            $query,
            -1,
            $count,
            PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );

        return $items;
    }

    /**
     * @param string $template
     * @return string
     */
    private function quote(string $template): string
    {
        return '/' . str_replace('/', '\/', $template) . '/';
    }

    /**
     * @param array $match
     * @param string $itemClass
     * @return AbstractTemplate
     * @throws ReflectionException
     */
    private function createItem(array $match, string $itemClass): AbstractTemplate
    {
        unset($match[0]);

        return $this->classCreatorService->createSingle($itemClass, array_column($match, self::MATCH_STRING));
    }
}
