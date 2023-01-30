<?php

declare(strict_types=1);

namespace Tests;

use EugeneErg\Preparer\Collections\RequiredCollection;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Data\Table;
use EugeneErg\Preparer\Queries\SelectQuery;
use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\ValueObjects\Branch;
use EugeneErg\Preparer\ValueObjects\Required;
use PHPUnit\Framework\TestCase;

final class RequiredTest extends TestCase
{
    /** @dataProvider getStructureData */
    public function testGetStructure(Returning $returning, RequiredCollection $expected): void
    {
        $structure = Branch::getStructure($returning->source);

        var_dump($structure);die;

        $actual = Required::getStructure($returning, $structure);
        $this->assertEquals($expected, $actual);
    }

    public function getStructureData(): array
    {
        $table = new Table('table');
        $returning = new Returning(new TypeCollection([
            'alias' => $table->getString('field'),
        ]), (new SelectQuery())->from($table));
        $expected = new RequiredCollection();

        return [
            [$returning, $expected],
        ];
    }
}
