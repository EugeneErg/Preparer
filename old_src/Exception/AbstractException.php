<?php namespace EugeneErg\Preparer\Exception;

use Exception;
use Throwable;

abstract class AbstractException extends Exception
{
    public const PATTERN = '%s';

    /**
     * AbstractException constructor.
     * @param array $values
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $values = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct(vsprintf(static::PATTERN, $values), $code, $previous);
    }
}