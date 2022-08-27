<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Enums;

enum JoinTypeEnum: string
{
    case Correlate = 'correlate';
    case Left = 'left';
    case Right = 'right';
    case Inner = 'inner';
    case Outer = 'outer';
}
