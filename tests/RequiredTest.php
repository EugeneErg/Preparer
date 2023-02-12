<?php

declare(strict_types=1);

namespace Tests;

use EugeneErg\Collections\CollectionInterface;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Data\Table;
use EugeneErg\Preparer\DataTransferObjects\Destination;
use EugeneErg\Preparer\DataTransferObjects\Select;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Queries\SelectQuery;
use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use EugeneErg\Preparer\ValueObjects\Branch;
use EugeneErg\Preparer\ValueObjects\Required;
use PHPUnit\Framework\TestCase;

final class RequiredTest extends TestCase
{
    /** @dataProvider getStructureData */
    public function testGetStructure(Returning $returning, array $expected): void
    {
        $structure = Branch::getStructure($returning->source);
        $actual = Required::getStructure($returning, $structure);

        $this->assertEquals($expected, $this->collectionToArray($actual->values(), [$this, 'requiredToArray']));
    }

    public function getStructureData(): array
    {
        $table = new Table('table');
        $getField = $table->getString('field');
        $query = (new SelectQuery())->from($table);
        $returning = new Returning(new TypeCollection([
            'alias' => $getField,
        ]), $query);
        $destination = [
            'query' => $query,
            'children' => [[
                'query' => $table,
                'children' => [],
                'level' => 2,
            ]],
            'level' => 1,
        ];
        $expected = [
            [
                'destinations' => [[
                    'path' => [],
                    'destination' => $destination,
                    'alias' => 'alias',
                ]],
                'used' => [],
                'executionRange' => [$destination],
                'target' => $getField->getParent(),
            ],
            [
                'destinations' => [[
                    'path' => [],
                    'destination' => $destination,
                    'alias' => null,
                ]],
                'used' => [],
                'executionRange' => [$destination],
                'target' => $query->getChildren()[0],
            ],
        ];

        return [
            [$returning, $expected],
        ];
    }

    private function collectionToArray(CollectionInterface $values, callable $callback): array
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[$key] = $callback($value);
        }

        return $result;
    }

    private function requiredToArray(Required $value): array
    {
        return [
            'destinations' => $this->collectionToArray($value->destinations, [$this, 'destinationToArray']),
            'used' => $this->collectionToArray($value->used, [$this, 'selectToArray']),
            'executionRange' => $this->collectionToArray($value->executionRange, [$this, 'branchToArray']),
            'target' => $this->functionToArray($value->target),
        ];
    }

    private function destinationToArray(Destination $value): array
    {
        return [
            'path' => $this->collectionToArray($value->path, [$this, 'branchToArray']),
            'destination' => $this->branchToArray($value->destination),
            'alias' => $value->alias,
        ];
    }

    private function selectToArray(Select $select): array
    {
        return [
            'path' => $this->collectionToArray($select->path, [$this, 'branchToArray']),
            'method' => $this->functionToArray($select->method),
        ];
    }

    private function branchToArray(Branch $branch): array
    {
        return [
            'query' => $this->queryToArray($branch->query),
            'children' => $this->collectionToArray($branch->children, [$this, 'branchToArray']),
            'level' => $branch->level,
        ];
    }

    private function functionToArray(AbstractFunction $target): AbstractFunction
    {
        return $target;
    }

    private function queryToArray(QueryTypeInterface $query): QueryTypeInterface
    {
        return $query;
    }
}
