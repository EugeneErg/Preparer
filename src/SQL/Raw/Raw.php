<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\SQL\Query\AbstractQuery;
use EugeneErg\Preparer\SQL\Query\AbstractSource;
use EugeneErg\Preparer\SQL\Query\InsertQuery;
use EugeneErg\Preparer\SQL\Query\SelectQuery;
use EugeneErg\Preparer\SQL\Query\SubQueryInterface;
use EugeneErg\Preparer\SQL\Query\Table;
use EugeneErg\Preparer\SQL\Query\Union;
use EugeneErg\Preparer\SQL\Query\Values;
use EugeneErg\Preparer\SQL\Raw\Templates\CommandTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\CommentTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\ContextTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\MethodTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\NumberTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\OperatorTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\ParenthesisTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\PunctuationTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\RecordTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\StringTemplate;
use EugeneErg\Preparer\SQL\Structures\Command;
use EugeneErg\Preparer\SQL\Structures\Parenthesis;
use EugeneErg\Preparer\SQL\Query\SubQuery;
use EugeneErg\Preparer\SQL\ValueInterface;

use EugeneErg\Preparer\SQL\Values;
use function in_array;

class Raw extends AbstractRaw implements ValueInterface
{
    /**
     * @var array
     */
    private array $aliases;

    public function __construct(string $query)
    {
        parent::__construct($query, [
            StringTemplate::class,
            RecordTemplate::class,
            MethodTemplate::class,
            ParenthesisTemplate::class,
            NumberTemplate::class,
            CommandTemplate::class,
            CommentTemplate::class,
            OperatorTemplate::class,
            PunctuationTemplate::class,
        ], ContextTemplate::class);
    }

    /** @inheritDoc*/
    protected function templatesToSubQuery(array $templates): SubQuery
    {
        foreach ($templates as $template) {
            switch ($template->getName()) {
                //case
            }
        }
    }

    /**
     * @inheritDoc
     * @throws RawException
     */
    protected function templatesToQuery(array $templates): AbstractQuery
    {
        $select = [
            'correlate' => '.',
            'from' => [
                '(left |right |inner |outer |)join' => [
                    'on' => "..",
                    ".",
                    "../where",
                    "../group by",
                    "../order by",
                    '../limit',
                ],
                'where' => [
                    "../group by",
                    "../order by",
                    '../limit',
                ],
                'group by' => [
                    'having' => [
                        "../order by",
                        '../limit',
                    ],
                    "../order by",
                    '../limit',
                ],
                'order by' => [
                    '../limit',
                ],
                'limit',
            ],
        ];
        $structures = [[
                'select' => $select,
            ], [
                'update' => [
                    'set' => $select,
                ],
            ], [
                'delete'=> $select,
            ], [
                'insert into' => [
                    'set' => $select,
                ],
            ],
        ];
        $branches = [];

        foreach ($structures as $structure) {
            $branches[] = new Structure($structure);
        }

        $commands = $this->createCommands($this->createStructure($templates), ...$branches);
        $this->aliases = [];

        if ($commands === null) {
            throw new RawException('invalid query');
        }

        switch ($commands[0]->getName()) {
            case 'select': return $this->createSelectQuery($commands);
            case 'insert into': return $this->createInsertQuery($commands);
            case 'update': return $this->createUpdateQuery($commands);
            case 'delete': return $this->createDeleteQuery($commands);
        }
    }

    /** @inheritDoc*/
    protected function templatesToValue(array $templates): ValueInterface
    {

    }

    /**
     * @param AbstractTemplate[] $templates
     * @param int $position
     * @return Parenthesis[]|AbstractTemplate[]
     * @throws RawException
     */
    private function createStructure(array $templates, &$position = 0): array
    {
        for ($result = []; $position < count($templates); $position++) {
            $template = $templates[$position];

            if ($template instanceof ParenthesisTemplate) {
                if (!$template->isOpen()) {
                    return $result;
                }

                $position++;
                $includes = $this->createStructure($templates, $position);

                if (!isset($templates[$position])
                    || !$templates[$position] instanceof ParenthesisTemplate
                    || $templates[$position]->getType() !== $template->getType()
                    || $templates[$position]->isOpen()
                ) {
                    throw new RawException();
                }

                $result[] = new Parenthesis($template->getType(), $includes);
            } else {
                $result[] = $template;
            }
        }

        return $result;
    }

    /**
     * @param Parenthesis[]|AbstractTemplate[] $commands
     * @param Structure ...$structures
     * @return Command[]
     */
    private function createCommands(array $commands, Structure ...$structures): ?array
    {
        foreach ($structures as $structure) {
            $result = $this->createCommandsByPosition($commands, $structure);

            if ($result !== null) {
                return $result;
            }
        }

        return null
    }

    /**
     * @param Structure $structure
     * @return string[][]
     */
    private function getGroups(Structure $structure): array
    {
        $commands = array_map(function(string $command) {
            return explode(' ', $command);
        }, $structure->getChildNames());
        usort($commands, function(array $groupA, array $groupB): int {
            return count($groupA) <=> count($groupB);
        });

        return $commands;
    }

    /**
     * @@param Command[] $commands
     * @return SelectQuery
     */
    private function createSelectQuery(array $commands): SelectQuery
    {
        $limits = $this->commandByName($commands, 'limit');
        list($limit, $offset) = $this->getLimitOffset($limits[0]->getIncludes() ?? []);

        $select = $commands[0]->getIncludes();
        $distinct = $select[0] instanceof CommandTemplate && $select[0]->getValue() === 'distinct';

        if ($distinct) {
            unset($select[0]);
        }

        $values = [];
        $this->explode(',', $select, function(array $part) use(&$values) {
            list($alias, $value) = $this->createSelectValue($part);
            $values[$alias] = $value;
        });
        $result = new SelectQuery($values, $distinct, $limit, $offset);
        $this->createSubQueries($result, $commands);

        foreach ($commands as $command) {
            switch ($command->getName()) {
                case 'order by':
                    $this->explode(',', $command->getIncludes(), function(array $orderBy) use($result) {
                        list($value, $direction) = $this->createOrderByValue($orderBy);
                        $result->orderBy($value, $direction);
                    });

                    break;
                case 'group by':
                    $this->explode(',', $command->getIncludes(), function(array $groupBy) use($result) {
                        $result->groupBy($this->createValue($groupBy));
                    });

                    break;
                case 'where':
                case 'on':
                case 'having':
                    $result->where($this->createValue($command->getIncludes()));
            }
        }

        return $result;
    }

    /**
     * @param AbstractQuery $query
     * @param Command[] $commands
     */
    private function createSubQueries(AbstractQuery $query, array $commands): void
    {
        foreach ($commands as $command) {
            $commandOptions = explode(' ', $command->getName());

            if (count($commandOptions) === 1) {
                $join = null;
                $type = $commandOptions[0];
            } else {
                $join = $commandOptions[0];
                $type = $commandOptions[1];
            }

            if ($type === 'from') {
                $this->explode(',', $command->getIncludes(), function(array $from) use($query) {
                    $query->from($this->createSource($from));
                });
            } elseif (in_array($type, ['join', 'correlate'], true)) {
                $query->from($this->createSource($command->getIncludes()), $join ?? $type);
            }
        }
    }

    /**
     * @@param Command[] $commands
     * @return InsertQuery
     */
    private function createInsertQuery(array $commands): InsertQuery
    {
        $insertCommand = array_shift($commands);
    }

    /**
     * @@param Command[] $commands
     * @return Container
     */
    private function createUpdateQuery(array $commands): Container
    {
        $updateCommand = array_shift($commands);
    }

    /**
     * @@param Command[] $commands
     * @return Container
     */
    private function createDeleteQuery(array $commands): Container
    {
        $deleteCommand = array_shift($commands);
    }

    private function splitByPunctuation(array $templates, string $punctuation): array
    {
        $result = [];
        $items = [];

        foreach ($templates as $template) {
            if ($template instanceof PunctuationTemplate
                && $template->getValue() === $punctuation
                && !empty($items)
            ) {
                $result[] = $items;
            } else {
                $items[] = $template;
            }
        }

        if (!empty($items)) {
            $result[] = $items;
        }

        return $result;
    }

    private function createBranch(array $structure, array $path = [], array $branches = []): Structure
    {
        foreach ($structure as $action => $configuration) {
            if (is_numeric($action) && ) {

            }
        }
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
    }

    /**
     * @param Parenthesis[]|AbstractTemplate[] $templates
     * @param Structure $structure
     * @return Command[]
     */
    private function createCommandsByPosition(array $templates, Structure $structure): ?array
    {
        $includes = [];
        $result = null;
        $commandName = null;

        for ($position = 0; $position < count($templates); $position++) {
            $template = $templates[$position];

            if (!$template instanceof CommandTemplate) {
                if ($commandName === null) {
                    $childStructure = $structure->findChild('');

                    if ($childStructure === null) {
                        return null;
                    }

                    $commandName = '';
                    $structure = $childStructure;
                }

                $includes[] = $template;
            }

            $newCommandName = (empty($includes) ? $commandName . ' ' : '') . $template->getValue();
            $childStructure = $structure->findChild($newCommandName);

            if ($childStructure === null && count($result) === 0) {
                return null;
            }

            if ($childStructure !== null) {
                if (!empty($includes)) {
                    $result[] = new Command($commandName, $includes);
                    $includes = [];
                }

                $commandName = $newCommandName;
            } elseif (empty($includes)) {
                $structure = $structure->findChild($commandName);
                $position--;
            } else {
                $includes[] = $template;
            }
        }

        if (!empty($includes)) {
            if ($commandName === null) {
                if ($structure->findChild('') === null) {
                    return null;
                }

                $commandName = '';
            }

            $result[] = new Command($commandName, $includes);
        }

        return $result;
    }

    /**
     * @param Command[] $commands
     * @param string $string
     * @return Command[]
     */
    private function commandByName(array $commands, string $string):array
    {
        $result = [];

        foreach ($commands as $command) {
            if ($command->getName() === $string) {
                $result[] = $command;
            }
        }

        return $result;
    }

    /**
     * @param Parenthesis[]|AbstractTemplate[] $includes
     * @return AbstractSource
     * @throws RawException
     */
    private function createSource(array $includes): AbstractSource
    {
        if (!isset($includes[0])) {
            throw new RawException('invalid query');
        }

        if ($includes[0] instanceof RecordTemplate) {
            if (count($includes) > 1) {
                throw new RawException('invalid query');
            }

            $result = $includes[0]->getValue();

            if (!$result instanceof ) {
                throw new RawException('invalid query');
            }

            return $result;
        }

        $commands = $this->createCommands($includes, new Structure([
            'values|union( all)|' => [
                'as',
            ],
        ]));

        if (empty($commands)) {
            throw new RawException('invalid query');
        }

        $query = $commands[0];
        $alias = $this->getAliasFromAs($commands[1]->getIncludes() ?? []);

        switch ($query->getName()) {
            case 'values':
                $values = $this->explode(',', $query->getIncludes(), function(array $value) {
                    return $this->createValues($value);
                });
                $result = new Values(...$values);
                $this->setQuery($result, $alias);

                return $result;
            case 'union':
            case 'union all':
                $unions = $this->explode(',', $query->getIncludes(), function(array $union) {
                    return $this->createSelectSubQuery($union);
                });
                $result = new Union($query->getName() === 'union all', ...$unions);
                $this->setQuery($result, $alias);

                return $result;
            default:
                list($name, $schema, $base) = $this->getTableOptions($query);
                $result = new Table($name, $schema, $base);
                $this->setQuery($result, $alias ?? $name);

                return $result;
        }
    }

    /**
     * @param AbstractTemplate[] $as
     * @return string
     * @throws RawException
     */
    private function getAliasFromAs(array $as): string
    {
        if (count($as) !== 1) {
            throw new RawException();
        }

        if ($as[0] instanceof CommandTemplate
            || $as[0] instanceof StringTemplate
        ) {
            return $as[0]->getValue();
        }

        throw new RawException();
    }

    /**
     * @param string $string
     * @param AbstractTemplate[]|Parenthesis[] $includes
     * @param \Closure $callback
     * @return array
     */
    private function explode(string $string, array $includes, \Closure $callback): array
    {
        $part = [];
        $result = [];

        foreach ($includes as $include) {
            if ($include instanceof PunctuationTemplate
                && $include->getValue() === $string
            ) {
                $result[] = $callback($part);
                $part = [];
            } else {
                $part[] = $include;
            }
        }

        return $result;
    }

    /**
     * @param AbstractTemplate[]|Parenthesis[] $values
     * @return array
     */
    private function createValues(array $values): array
    {
        'select {
            name: q.0,
            z: 23,
            456,
            12345,
        } from values [ 
            {},
            {},
        ] as q
        
        update {
            
        }
        
        
        '
        /*
         *
         *
         *
        (
            12 as q,
            123 as z

        )
        */

        $this->explode(',', $values, function($value) {

        });
    }
}
