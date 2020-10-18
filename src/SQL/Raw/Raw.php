<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\SQL\OldQuery;
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

use function in_array;

class Raw extends AbstractRaw
{
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
    protected function templatesToSubQuery(array $templates): Container
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
    protected function templatesToQuery(array $templates): Container
    {
        $structures = [
            $select = new Structure(),
            $from = new Structure(),
            $orderBy = new Structure(),
            $groupBy = new Structure(),
            $where = new Structure(),
            $join = new Structure(),
            $tree = new Structure(),
            $having = new Structure(),
            $update = new Structure(),
            $insert = new Structure(),
        ];
        $tree->addChildren([
            'select' => $select,
            'update' => $update,
            'delete'=> $select,
            'insert into' => $insert,
        ]);
        $select->addChildren([
            'from' => $from,
            'correlate' => $select,
        ]);
        $from->addChildren([
            'join' => $join,
            'where' => $where,
            'group by' => $groupBy,
            'order by' => $orderBy,
            'limit',
        ]);
        $join->addChildren([
            'on' => $from,
            'join' => $join,
            'where' => $where,
            'group by' => $groupBy,
            'order by' => $orderBy,
            'limit',
        ]);
        $where->addChildren([
            'group by' => $groupBy,
            'order by' => $orderBy,
            'limit',
        ]);
        $groupBy->addChildren([
            'having' => $having,
            'order by' => $orderBy,
            'limit',
        ]);
        $having->addChildren([
            'order by' => $orderBy,
            'limit',
        ]);
        $orderBy->addChild('limit');
        $update->addChild('set', $select);
        $insert->addChild('set', $select);

        $postfixes = [
            'union' => ['correlate', 'from', 'join'],
        ];

        foreach ($postfixes as $postfix => $commands) {
            foreach ($structures as $structure) {
                foreach (array_intersect($commands, $structure->getChildNames()) as $command) {
                    $structure->addChild(
                        $command . ' ' . $postfix,
                        $structure->getChild($command)
                    );
                }
            }
        }

        $prefixesByCommand = [
            'join' => ['left', 'right', 'outer', 'inner'],
            'join union' => ['left', 'right', 'outer', 'inner'],
        ];

        foreach ($prefixesByCommand as $command => $prefixes) {
            foreach ($structures as $structure) {
                if (in_array($command, $structure->getChildNames(), true)) {
                    foreach ($prefixes as $prefix) {
                        $structure->addChild(
                            $prefix . ' ' . $command,
                            $structure->getChild($command)
                        );
                    }
                }
            }
        }

        $commands = $this->splitByCommands($this->createStructure($templates), $tree);

        if (!isset($commands[0])
            || !in_array($commands[0]->getName(), [
                'select', 'insert into', 'update', 'delete'
            ], true)
        ) {
            throw new RawException();
        }

        switch ($commands[0]->getName()) {
            case 'select': return $this->createSelectQuery($commands);
            case 'insert into': return $this->createInsertQuery($commands);
            case 'update': return $this->createUpdateQuery($commands);
            case 'delete': return $this->createDeleteQuery($commands);
        }
    }

    /** @inheritDoc*/
    protected function templatesToValue(array $templates): Container
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
        $result = [];

        for (; $position < count($templates); $position++) {
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
     * @param Parenthesis[]|AbstractTemplate[] $structure
     * @param Structure $commandsTree
     * @return Command[]
     */
    private function splitByCommands(array $structure, Structure $commandsTree): array
    {
        $commands = $this->getGroups($commandsTree);
        $result = [];
        $includes = [];
        $commandName = null;

        for($position = 0; $position < count($structure); $position++) {
            $template = $structure[$position];

            if ($template instanceof CommandTemplate) {
                foreach ($commands as $group) {
                    foreach ($group as $number => $command) {
                        if (!isset($structure[$position + $number])
                            || !$structure[$position + $number] instanceof CommandTemplate
                            || $command !== $structure[$position + $number]->getValue()
                        ) {
                            continue(2);
                        }
                    }

                    if (!empty($includes)) {
                        $result[] = new Command($commandName, $includes);
                        $includes = [];
                    }

                    $commandName = implode(' ', $group);
                    $position += count($group) - 1;
                    $commands = isset($commandsTree[$commandName]) ? array_map(function(string $command) {
                        return explode(' ', $command);
                    }, $commandsTree[$commandName]) : [];

                    continue(2);
                }
            }

            $includes[] = $template;
        }

        if (!empty($includes)) {
            $result[] = new Command($commandName, $includes);
        }

        return $result;
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
     * @return Container
     */
    private function createSelectQuery(array $commands): Container
    {
        $query = new OldQuery();

        foreach ($commands as $command) {
            switch ($command->getName()) {
                case 'select':

                case 'order by':

                case 'group by':

                case 'where':
                case 'on':
                case 'having':

                case 'limit':

                default:
                    $keys = explode(' ', $command->getName(), 3);

                    switch (count($keys)) {
                        case 1:
                            $commandName = $keys[0];
                            $join = null;
                            $union = false;

                            break;
                        case 2:
                            if ($keys[1] === 'union') {
                                $commandName = $keys[0];
                                $join = null;
                                $union = true;
                            } else {
                                $commandName = $keys[1];
                                $join = $keys[0];
                                $union = false;
                            }

                            break;
                        default:
                            $commandName = $keys[1];//join
                            $join = $keys[0];
                            $union = true;
                    }

                    if ($union) {

                    }

                /**
                 * left from union all(
                 *     (
                 *         select
                 *             qwe as q
                 *         from table1
                 *     ),
                 *     (
                 *
                 *     )
                 *
                 *
                 * )
                 *
                 *
                 *
                 */






            }
        }

        $selectCommand = array_shift($commands);
        $selects = $this->splitByPunctuation(
            $selectCommand->getIncludes(),
            PunctuationTemplate::VALUE_COLON
        );
        $selectValues = [];

        foreach ($selects as $select) {
            if (count($select) > 3
                && $select[count($select) - 2] instanceof CommandTemplate
                && $select[count($select) - 2]->getValue() === 'as'
                && $select[count($select) - 1] instanceof StringTemplate
            ) {
                $alias = $select[count($select) - 1]->getValue();
                $query->select($alias, '');
                $selectValues[$alias] = array_slice($select, -3);
            } else {
                $selectValues[] = $select;
                end($selectValues);
                $query->select(key($selectValues), '');
            }
        }







        /**
         * select
         *  q as qwe,
         *  z as rgt,
         *  wrrtfer
         * from
         * join
         * on
         * where
         * group by
         * having
         * order by
         */
    }

    /**
     * @@param Command[] $commands
     * @return Container
     */
    private function createInsertQuery(array $commands): Container
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
}
/**
 * $union = new union(true);//union all
 * $union = new union();//union
 * $union->select('field1', $table->field, $table2->field)
 */
