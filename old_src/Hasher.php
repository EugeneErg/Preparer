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
    public function getHash($object, string $prefix = '$', string $postfix = '$')
    {
        $hash = $prefix . spl_object_hash($object) . $postfix;
        self::$objects[$hash] = $object;

        return $hash;
    }

    /**
     * @param string $hash
     * @return object
     */
    public function getObject(string $hash): object
    {
        return self::$objects[$hash];
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function hasObject(string $hash): bool
    {
        return isset(self::$objects[$hash]);
    }
}
