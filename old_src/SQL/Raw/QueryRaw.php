<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Collection;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\SQL\Query\AbstractQuery;
use EugeneErg\Preparer\SQL\Query\AbstractSource;
use EugeneErg\Preparer\SQL\Query\InsertQuery;
use EugeneErg\Preparer\SQL\Query\ReturningQuery;
use EugeneErg\Preparer\SQL\Query\SelectQuery;
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
use EugeneErg\Preparer\SQL\ValueInterface;

use function in_array;

class QueryRaw extends AbstractQueryRaw
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
    protected function templatesToSubQuery(Collection $templates): AbstractSource
    {
        $commands = $this->createCommands($this->createStructure($templates), new Structure([
            'insert into|update' => [
                'set' => '../delete',
            ],
            'delete' => [
                'correlate' => [
                    '.',
                    '../from',
                ],
                'from' => [
                    '(left |right |inner |outer |)join' => [
                        'on' => "..",
                        ".",
                        "../where",
                        "../order by",
                        '../limit',
                    ],
                    'where' => [
                        "../order by",
                        '../limit',
                    ],
                    'order by' => [
                        '../limit',
                    ],
                    'limit',
                ],
            ],
            'select( distinct)?' => [
                'correlate' => [
                    '.',
                    '../from',
                ],
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
            ],
            'union( all)?|values|' => [],
        ]));

        switch ($commands[0]->getName()) {
            case 'insert into':
                return $this->createInserQuery($commands);
            case 'update':
                return $this->createUpdateQuery($commands);
            case 'select':
                return $this->createSelectQuery($commands, false);
            case 'select distinct':
                return $this->createSelectQuery($commands, true);
            case 'delete':
                return $this->createDeleteQuery($commands);
            case 'union':
                return $this->createUnion($commands, false);
            case 'union all':
                return $this->createUnion($commands, true);
            case 'values':
                return $this->createValues($commands);
            default:
                return $this->createTable($commands);
        }
    }

    /**
     * @inheritDoc
     * @throws RawException
     */
    protected function templatesToQuery(array $templates): ReturningQuery
    {
        $commands = $this->createCommands($this->createStructure($templates), new Structure([
            'insert into|update' => [
                'set' => '../select( distinct)?|delete'
            ],
            'select( distinct)?|delete' => [
                'correlate' => [
                    '.',
                    '../from',
                    '../returning',
                ],
                'from' => [
                    '(left |right |inner |outer |)join' => [
                        'on' => "..",
                        ".",
                        "../where",
                        "../group by",
                        "../order by",
                        '../limit',
                        '../returning',
                    ],
                    'where' => [
                        "../group by",
                        "../order by",
                        '../limit',
                        '../returning',
                    ],
                    'group by' => [
                        'having' => [
                            "../order by",
                            '../limit',
                            '../returning',
                        ],
                        "../order by",
                        '../limit',
                        '../returning',
                    ],
                    'order by' => [
                        '../limit',
                        '../returning',
                    ],
                    'limit',
                    '../returning',
                ],
                '../returning',
            ],
            'returning' => [],
        ]));

        $returning = $this->commandByName($commands, 'returning');

        if (count($returning) !== 1) {
            throw new RawException();
        }

        $selects = $this->createArrayFromJson($returning[0]);

        switch ($commands[0]->getName()) {
            case 'insert into':
                $result = $this->createInserQuery($commands);

                break;
            case 'update':
                $result = $this->createUpdateQuery($commands);

                break;
            case 'select':
                $result = $this->createSelectQuery($commands, false);

                break;
            case 'select distinct':
                $result = $this->createSelectQuery($commands, true);

                break;
            case 'delete':
                $result = $this->createDeleteQuery($commands);

                break;
            default:
                $result = new SelectQuery();
        }

        return new ReturningQuery($result, $selects);
    }

    /** @inheritDoc*/
    protected function templatesToValue(array $templates): ValueInterface
    {
        // математическое, логическое выражение
    }

    /**
     * @param AbstractTemplate[]|Collection $templates
     * @return Parenthesis[]|AbstractTemplate[]|Collection
     * @throws RawException
     */
    private function createStructure(Collection $templates): Collection
    {
        try {
            return $templates->tree(function ($template): ?bool {
                return $template instanceof ParenthesisTemplate
                    ? $template->isOpen()
                    : null;
            }, function (Collection $collection): Parenthesis {
                /**
                 * @var ParenthesisTemplate $last
                 * @var ParenthesisTemplate $first
                 */
                $last = $collection->wind();
                $first = $collection->rewind();

                if ($last->getType() !== $first->getType()) {
                    throw new RawException('the type of the open and close parenthesis does not match');
                }

                return new Parenthesis($first->getType(), $collection->slice(1, -1));
            });
        } catch (\Exception $exception) {
            throw new RawException($exception->getMessage());
        }
    }

    /**
     * @param Parenthesis[]|AbstractTemplate[]|Collection $commands
     * @param Structure $structure
     * @return Collection|Command[]|null
     */
    private function createCommands(Collection $commands, Structure $structure): ?Collection
    {
        $templates = $commands->trim(function($item): bool {
            return $item instanceof ContextTemplate;
        });




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
                    $result[] = new Command($commandName, $this->trim($includes));
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

            $result[] = new Command($commandName, $this->trim($includes));
        }

        return new Collection($result);
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
     * @param Command[] $commands
     * @param bool $distinct
     * @return SelectQuery
     */
    private function createSelectQuery(array $commands, bool $distinct): SelectQuery
    {
        $limits = $this->commandByName($commands, 'limit');
        list($limit, $offset) = $this->getLimitOffset($limits[0]->getIncludes() ?? []);
        $result = new SelectQuery($distinct, $limit, $offset);


        //templatesToSubQuery

        $this->createSubQueries(
            $result,
            $this->commandByName($commands, 'from|correlate|(left |right |inner |outer |)join')
        );

        foreach ($commands as $command) {
            switch ($command->getName()) {
                case 'order by':
                    $this->explode(
                        ',', $command->getIncludes(),
                        function(array $orderBy) use($result): void {
                            $result->orderBy(...$this->createOrderBy($orderBy));
                        }
                    );

                    break;
                case 'group by':
                    $this->explode(
                        ',', $command->getIncludes(),
                        function(array $groupBy) use($result): void {
                            $result->groupBy($this->createValue($groupBy));
                        }
                    );

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
            if (is_numeric($action)  ) {

            }
        }
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
    }

    /**
     * @param Command[] $commands
     * @param string $commandName
     * @return Command[]
     */
    private function commandByName(array $commands, string $commandName): array
    {
        $result = [];

        foreach ($commands as $command) {
            if (preg_match(
                '/^' . str_replace('/', '\\/', $commandName) . '$/',
                $command->getName()
            ) !== 0) {
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
     * @param int|null $limit
     * @return array
     */
    private function explode(string $string, array $includes, \Closure $callback, ?int $limit = null): array
    {
        $part = [];
        $result = [];

        foreach ($includes as $include) {
            if ($include instanceof PunctuationTemplate
                && $include->getValue() === $string
                && ($limit === null || count($result) < $limit - 1)
            ) {
                $key = null;
                $item = $callback($part, $key);

                if ($key === null) {
                    $result[] = $item;
                } else {
                    $result[$key] = $item;
                }

                $part = [];
            } else {
                $part[] = $include;
            }
        }

        if (count($part)) {
            $key = null;
            $item = $callback($part, $key);

            if ($key === null) {
                $result[] = $item;
            } else {
                $result[$key] = $item;
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

    private function createArrayFromJson(Command $returning): array
    {
        $includes = $returning->getIncludes();

        if (count($includes) !== 1) {
            throw new RawException();
        }

        $json = $includes[0];

        if (!$json instanceof Parenthesis
            || $json->getName() !== ParenthesisTemplate::TYPE_CURLY
        ) {
            throw new RawException();
        }

        $includes = $json->getIncludes();
        $lastTemplate = end($includes);

        if ($lastTemplate instanceof PunctuationTemplate
            && $lastTemplate->getValue() === PunctuationTemplate::VALUE_DOT
        ) {
            array_shift($includes);
        }

        return $this->explode(',', $json->getIncludes(), function(array $partials, ?string &$key) {
            $results = $this->explode(':', $partials, function(array $partials) {
                return $partials;
            }, 2);

            if (count($results) !== 2
                || count($results[0]) !== 1
            ) {
                throw new RawException();
            }

            $keyTemplate = $results[0][0];

            if (!$keyTemplate instanceof CommandTemplate
                && (!$keyTemplate instanceof StringTemplate
                    || $keyTemplate->getQuote() !== StringTemplate::QUOTE_DOUBLE
                )
            ) {
                throw new RawException();
            }

            $key = $keyTemplate->getValue();

            return $this->createValuesSubQuery($results[1]);
        });
    }

    /**
     * @param Parenthesis[]|AbstractTemplate[] $commands
     * @return Parenthesis[]|AbstractTemplate[]
     */
    private function trim(array $commands): array
    {
        return array_slice(
            $commands,
            $this->getClearCount($commands),
            - $this->getClearCount(array_reverse($commands))
        );
    }

    /**
    /**
     * @param Parenthesis[]|AbstractTemplate[] $commands
     * @return int
     */
    private function getClearCount(array $commands): int
    {
        $result = 0;

        foreach ($commands as $command) {
            if (!$command instanceof ContextTemplate) {
                break;
            }

            $result++;
        }

        return $result;
    }

    private function strPos(array $includes, \Closure $needle, int $offset = 0, bool $isReverse = false): ?int
    {
        for (
            $includes = $isReverse ? array_reverse($includes) : array_values($includes);
            $offset < count($includes);
            $offset++
        ) {
            $result = $needle($includes[$offset]);

            if (is_bool($result)) {
                return $result ? $offset : null;
            }
        }

        return null;
    }

    private function createOrderBy(array $orderBy): array
    {



        $directionPos = $this->strPos($orderBy, function($item): ?bool {
            return $item instanceof ContextTemplate
                ? null
                : $item instanceof CommandTemplate
                    && in_array($item->getValue(), ['desc', 'asc'], true);
        }, 0, true) === null;

        if ($directionPos !== null) {
            $hasDot = $this->strPos($orderBy, function($item): ?bool {
                return $item instanceof ContextTemplate
                    ? null
                    : $item instanceof PunctuationTemplate
                        && $item->getValue() === PunctuationTemplate::VALUE_DOT;
            }, $directionPos + 1, true) !== null;

            if ($hasDot) {
                $directionPos = null;
            }
        }

        if ($directionPos === null) {
            $direction = 'asc';
        } else {
            /** @var CommandTemplate $directionTemplate */
            $directionTemplate = array_splice(
                $orderBy,
                $directionPos === 0
                    ? count($orderBy) - 1
                    : - $directionPos
            )[0];
            $direction = $directionTemplate->getValue();
        }

        return [$this->createValue($orderBy), $direction];
    }
}
