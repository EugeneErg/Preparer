<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\DataTransferObjects\Select;

final class SelectCollection extends AbstractCollection
{
    protected const ITEM_TYPE = Select::class;
}
