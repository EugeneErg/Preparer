<?php namespace EugeneErg\Preparer;

class ValueIndexService
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @var $this
     */
    private static $instance;

    /**
     * @return static
     */
    public static function instance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param mixed ...$values
     * @return string
     */
    public function getIndex(...$values): string
    {
        $result = [];

        foreach ($values as $value) {
            $index = array_search($value, $this->values, true);

            if ($index === false) {
                $index = count($this->values);
                $this->values[] = $value;
            }

            $result[] = $index;
        }

        return implode('_', $result);
    }
}