<?php use EugeneErg\Preparer\Parser\AbstractTemplate;

namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\SQL\Query;
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
                case
            }
        }
    }

    /**
     * @inheritDoc
     * @throws RawException
     */
    protected function templatesToQuery(array $templates): Container
    {
        $tree = [
            '' => ['select', 'update', 'delete', 'insert into'],
            'select' => ['correlate', 'from'],
            'update' => ['set'],
            'insert into' => ['set'],
            'delete' => ['correlate', 'from'],
            'set' => ['correlate', 'from'],

            'correlate' => ['correlate', 'from'],
            'from' => ['join', 'where', 'group by', 'order by', 'limit'],
            'join' => ['on', 'join', 'where', 'group by', 'order by', 'limit'],
            'on' => ['join', 'where', 'group by', 'order by', 'limit'],
            'where' => ['group by', 'order by', 'limit'],
            'group by' => ['having', 'order by', 'limit'],
            'having' => ['order by', 'limit'],
        ];
        $postfixes = [
            'union all' => ['correlate', 'from', 'join'],
            'union' => ['correlate', 'from', 'join'],
        ];

        foreach ($postfixes as $postfix => $commands) {
            foreach ($tree as $parentCommand => $childCommands) {
                foreach (array_intersect($commands, $childCommands) as $command) {
                    $tree[$parentCommand][] = $command . ' ' . $postfix;
                }

                if (in_array($parentCommand, $commands, true)) {
                    $tree[$parentCommand . ' ' . $postfix] = $tree[$parentCommand];
                }
            }
        }

        $prefixesByCommand = [
            'join' => ['left', 'right', 'outer', 'inner'],
        ];

        foreach ($prefixesByCommand as $command => $prefixes) {
            foreach ($tree as $parentCommand => $childCommands) {
                if (in_array($command, $childCommands, true)) {
                    foreach ($prefixes as $prefix) {
                        $tree[$parentCommand][] = $prefix . ' ' . $command;
                    }
                }

                if (isset($tree[$command])) {
                    foreach ($prefixes as $prefix) {
                        $tree[$prefix . ' ' . $command][] = $tree[$command];
                    }
                }
            }
        }

        $commands = $this->splitByCommands($this->createStructure($templates), $tree);

        if (!isset($commands[0][0])
            || !in_array($commands[0][0], [
                'select', 'insert into', 'update', 'delete'
            ], true)
        ) {
            throw new RawException();
        }

        switch ($commands[0][0]->getValue()) {
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
     * @param Parenthesis[]|AbstractTemplate[]
     * @param string[][] $commandsTree
     * @return Command[]
     */
    private function splitByCommands(array $structure, array $commandsTree): array
    {
        $commands = array_map(function(string $command) {
            return explode(' ', $command);
        }, $commandsTree['']);
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
                    }

                    $includes[] = implode(' ', $group);
                    $position += count($group) - 1;

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
     * @@param Command[] $commands
     * @return Container
     */
    private function createSelectQuery(array $commands): Container
    {
        $query = new Query();
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

        $three = [
            'select' => ['from'],
            'from' => ['where', 'group by', 'order by', 'join', 'left join', 'right join', 'inner join', 'left join', 'left join', 'left join', 'left join', 'left join', 'left join', 'left join', 'left join', 'left join', 'left join', 'left join']


        ];




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
