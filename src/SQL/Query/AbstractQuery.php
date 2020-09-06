<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\SQL\Records\AbstractStructureRecord;
use EugeneErg\Preparer\SQL\AbstractQuery as AbstractQueryModel;
use EugeneErg\Preparer\SQL\Records\SubQueryRecord;
use EugeneErg\Preparer\SQL\Records\TableRecord;
use EugeneErg\Preparer\SQL\Table;
use EugeneErg\Preparer\SQL\Values;

abstract class AbstractQuery
{
    private AbstractQueryModel $query;
    private ?string $action = null;
    /** @var AbstractQuery[]  */
    private array $subQueries;
    private array $conditions;
    private array $sorts;
    private array $groups;
    private bool $distinct;
    private ?int $limit;
    private int $offset;

    public function __construct(
        AbstractStructureRecord $structureRecord,
        bool $distinct = false,
        int $limit = null,
        int $offset = 0
    ) {
        $this->query = $structureRecord->getQuery();
        $this->distinct = $distinct;
        $this->limit = $limit;
        $this->offset = $offset;

        /** @var Method $action */
        foreach ($structureRecord->getActions() as $action) {
            /**
             * @see AbstractQuery::from
             * @see AbstractQuery::where
             * @see AbstractQuery::orderBy
             * @see AbstractQuery::groupBy
             * @see AbstractQuery::delete
             * @see AbstractQuery::update
             * @see AbstractQuery::insert
             * @see AbstractQuery::select
             */
            $this->{$action->getName()}(...$action->getArguments());
        }
    }

    private function from(
        ?string $type,
        $query,
        int $limit = null,
        int $offset = 0,
        bool $distinct = false
    ): void {
        if (is_string($query)) {
            $this->subQueries[] = new SubQuery(

                //$query,
                $limit,
                $offset,
                $distinct
            );
        } elseif ($query instanceof Table) {
            $this->subQueries[] = new TableQuery(
                (new TableRecord($query))->getContainer(),
                $limit,
                $offset,
                $distinct
            );
        } elseif ($query instanceof Values) {
            $this->subQueries[] = new ValuesQuery(
                $query,
                $limit,
                $offset,
                $distinct
            );
        } elseif ($query instanceof SubQueryRecord) {
            $this->subQueries[] = new SubQuery(
                $query,
                $limit,
                $offset,
                $distinct
            );
        } else {
            throw new \Exception();
        }
    }

    private function where(): void
    {

    }

    private function orderBy(): void
    {

    }

    private function groupBy(): void
    {

    }

    private function delete(): void
    {
        $this->action = 'delete';
    }

    public function update(): void
    {
        $this->action = 'update';

    }

    public function insert(): void
    {
        $this->action = 'insert';

    }

    public function select(): void
    {
        $this->action = 'select';

    }
}
