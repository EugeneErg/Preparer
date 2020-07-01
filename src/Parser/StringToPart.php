<?php namespace EugeneErg\Preparer\Parser;

use Exception;

/**
 * Class StringToPart
 * @package EugeneErg\Preparer\Parser
 */
class StringToPart
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var array
     */
    private $shift = [];

    /**
     * @var TemplatePart[]
     */
    private $parts = [];

    /**
     * StringToPart constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
        $this->shift = [];

        for ($i = 0; $i < strlen($string); $i++) {
            $this->shift[$i] = $i;
        }
    }

    /**
     * @param int[] $shift
     * @param int $from
     * @param int $to
     * @return int[]
     * @throws Exception
     */
    private function shift(array $shift, int $from, int $to): array
    {
        for ($i = $from; $i <= $to; $i++) {
            if (!isset($shift[$i])) {
                throw new Exception('template is intersect');
            }

            unset($shift[$i]);
        }

        return $shift;
    }

    /**
     * @param int[] $shift
     * @return int[][]
     */
    private function getIntervals(array $shift): array
    {
        $intervals = [];
        $from = null;

        for ($i = 0; $i < count($this->shift); $i++) {
            if (isset($shift[$i]) !== isset($from)) {
                continue;
            }

            if (isset($shift[$i])) {
                $from = $i;
            }
            else {
                $intervals[] = [$from, $i - $from];
                $from = null;
            }
        }

        if (isset($from)) {
            $intervals[] = [$from, $i - $from];
        }

        return $intervals;
    }

    /**
     * @param int $from
     * @param int $to
     * @param bool $value
     * @param bool[] $map
     * @return bool[]
     */
    private function fill(int $from, int $to, bool $value, array $map = []): array
    {
        for ($i = $from; $i < $to; $i++) {
            $map[$i] = $value;
        }

        return $map;
    }

    /**
     * @param string[] $patternClasses
     * @return self
     * @throws Exception
     */
    public function createSub(array $patternClasses): self
    {
        /** @var TemplatePart[] $newParts */
        $newParts = [];
        $newString = $this->string;
        $newShift = $this->shift;

        foreach ($patternClasses as $pattern => $class) {
            preg_match_all(
                $pattern::TEMPLATE,
                $this->string,
                $matches,
                PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                if ($match[0][0] === '') {
                    continue;
                }

                $this->fill()

                for ($i = )
                $map = ;

                list($value, $from) = array_shift($match);


                foreach ($match as list($string, $position)) {
                    for ($i = 0; $i < strlen($string); $i++) {
                        $map[$this->shift[$i]] = false;
                    }
                }

                $valueLength = strlen($value);
                $newParts[] = new TemplatePart(
                    $class,
                    $this->shift[$from],
                    $this->shift[$valueLength + $from],
                    array_column($match, 0),
                    $map
                );
                $newShift = $this->shift($newShift, $from, $valueLength);
            }
        }

        foreach (array_reverse($this->getIntervals($newShift)) as list($start, $length)) {
            substr_replace($newString, '', $start, $length);
        }

        $new = clone $this;
        $new->parts = $newParts;
        $new->string = $newString;
        $new->shift = array_values($newShift);

        return $new;
    }

    /**
     * @return TemplatePart[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }
}
