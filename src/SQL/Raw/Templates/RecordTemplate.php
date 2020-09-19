<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class RecordTemplate extends AbstractTemplate
{
    public const TEMPLATE = '\\$[0-9a-z]{32}\\$';

}