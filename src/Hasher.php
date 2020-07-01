<?php namespace EugeneErg\Preparer;

/**
 * Class Hasher
 * @package EugeneErg\Preparer
 */
final class Hasher
{
    /**
     * @var object[]
     */
    private static $objects = [];

    /**
     * @param object $object
     * @param string $prefix
     * @param string $postfix
     * @return string
     */
    public static function getHash($object, string $prefix = '$', string $postfix = '$')
    {
        $hash = $prefix . spl_object_hash($object) . $postfix;
        self::$objects[$hash] = $object;

        return $hash;
    }

    /**
     * @param string $hash
     * @return object|null
     */
    public static function getObject(string $hash)
    {
        return self::$objects[$hash] ?? null;
    }

    private function __construct() {}
}
