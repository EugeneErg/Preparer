<?php namespace EugeneErg\Preparer\Parser;

use ReflectionException;
use ReflectionClass;

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
     * Parser constructor.
     * @param AbstractTemplate[]|string[] $templates
     * @param string $contextTemplateClass
     */
    public function __construct(array $templates, string $contextTemplateClass = ContextTemplate::class)
    {
        foreach ($templates as $itemClass) {
            $this->patterns[$this->quote($itemClass::TEMPLATE)] = $itemClass;
        }

        $this->contextTemplateClass = $contextTemplateClass;
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
                        $this->quote(($this->contextTemplateClass)::TEMPLATE),
                        substr($query, $position, $missed),
                        $matches,
                        PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
                    );

                    foreach ($matches as $contextMatch) {
                        $items[] = $this->createItem($contextMatch, $this->contextTemplateClass);
                    }

                    $items[] = $this->createItem(
                        [[],[
                            self::MATCH_STRING => substr($query, $position, $missed),
                            self::MATCH_POSITION => $position
                        ]],
                        ContextTemplate::class
                    );
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

        return (new ReflectionClass($itemClass))
            ->newInstanceArgs(array_column($match, self::MATCH_STRING));
    }
}
