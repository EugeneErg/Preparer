<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\DataTransferObjects\Select;

final class SelectCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Select::class;
}
