<?php namespace EugeneErg\Preparer;
/**
 * Class String
 * @package EugeneErg\Preparer
 */
class StringObject
{
    /**
     * @var string
     */
    private $string;

    /**
     * StringObject constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * @param int $start
     * @param int $length
     * @return StringObject|null
     */
    public function sub(int $start, int $length = null): ?self
    {
        $this->getNullOrSelf(substr($this->string, $start, $length));
    }

    /**
     * @return StringObject
     */
    public function toSnakeCase(): self
    {
        $lowerString = strtolower($this->string);

        for ($i = strlen($lowerString); $i >= 0; $i--) {
            if ($lowerString[$i] !== $this->string[$i]) {
                substr_replace($lowerString,'_',$i,0);
            }
        }

        return new self($lowerString);
    }

    /**
     * @param string $needle
     * @param bool $part
     * @return StringObject|null
     */
    public function chr(string $needle, bool $part = false): ?self
    {
        return $this->getNullOrSelf(strchr($this->string, $needle, $part));
    }

    /**
     * @param string $needle
     * @return StringObject|null
     */
    public function rchr(string $needle): ?self
    {
        return $this->getNullOrSelf(strrchr($this->string, $needle));
    }

    /**
     * @param string|false $value
     * @return StringObject|null
     */
    private function getNullOrSelf($value): ?self
    {
        return $value === false ? null : new self($value);
    }
}
