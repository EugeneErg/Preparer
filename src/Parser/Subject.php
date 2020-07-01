<?php namespace EugeneErg\Preparer\Parser;

/**
 * Class Subject
 * @package EugeneErg\Preparer\Parser
 */
class Subject
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * Subject constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Subject
     */
    public function getSubSubject(int $offset = 0, int $limit = null): self
    {
        $string = substr($this->getString(), $offset, $limit);

        if ($string === $this->getString() && $offset === 0) {
            return $this;
        }

        $result = new Subject($string);
        $result->offset = $this->offset + $offset;

        return $result;
    }
}
