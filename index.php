<?php
error_reporting(E_ALL);
(require __DIR__ . '/vendor/autoload.php')->addPsr4('EugeneErg\\Preparer\\', __DIR__ . '/src/');

use EugeneErg\Preparer\SQL\Query\SelectQuery;
use EugeneErg\Preparer\SQL\Query\ValuesQuery;
use EugeneErg\Preparer\SQL\Raw\Raw;

$value = new ValuesQuery([
    'id' => 12,
    'is_new' => false,
], [
    'id' => 23,
    'is_new' => true
]);

$q = (new SelectQuery([
    'id' => $value->id,
]))->from($value)
    ->where($value->is_new)
    ->where(new Raw("{$value->count()} > 1"));




