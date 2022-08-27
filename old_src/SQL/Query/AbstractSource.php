<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Hasher;
use EugeneErg\Preparer\SQL\Functions\Traits\FunctionTrait;

abstract class AbstractSource
{
    use FunctionTrait {
        __construct as private functionConstructor;
        getSource as private;
        getChildren as protected;
        call as protected;
    }

    private Hasher $hasher;

    public function __construct()
    {
        $this->functionConstructor($this);
        /** @var Hasher $hasher */
        $hasher = ClassCreatorService::instance()->createSingle(Hasher::class);
        $this->hasher = $hasher;
    }

    public function __toString(): string
    {
        return $this->hasher->getHash($this, ':', '');
    }
}
