<?php namespace EugeneErg\Preparer\Parser;

use EugeneErg\Preparer\TypeHelper;
use Exception;
use ReflectionException;

/**
 * Class Parser
 * Класс, для превращения текста в структуру
 * @package EugeneErg\Preparer
 */
class Parser
{
    private const MATCH_STRING = 0;
    private const MATCH_POSITION = 1;

    /**
     * @var string[]
     */
    private $valuePatterns;

    /**
     * @var string[]
     */
    private $structurePatterns;

    /**
     * Parser constructor.
     * @param string[] $templates
     */
    public function __construct(array $templates)
    {
        $this->valuePatterns = [];
        $this->structurePatterns = [];

        foreach ($templates as $template => $itemClass) {
            if (is_numeric($template)) {
                $template  = $itemClass::TEMPLATE;
            }

            $this->valuePatterns[$template] = $itemClass;
        }

        $this->valuePatterns[ContextTemplate::TEMPLATE] = ContextTemplate::class;
    }

    /**
     * @param string $query
     * @return MainTemplate
     * @throws \ReflectionException
     */
    public function parse(string $query): MainTemplate
    {
        return new MainTemplate($this->compileStructure($this->getStructure([new Subject($query)]), strlen($query)));
    }

    /**
     * @param string[] $patterns
     * @param Subject[] $subjects
     * @param \Closure $callback
     * @return Subject[]
     */
    private function parseByTemplates_old(array $patterns, array $subjects, \Closure $callback): array
    {
        foreach ($patterns as $pattern => $class) {
            $newSubjects = [];

            foreach ($subjects as $subject) {
                preg_match_all(
                    $pattern::TEMPLATE,
                    $subject->getString(),
                    $matches,
                    PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER
                );
                $offset = 0;

                foreach ($matches as $match) {
                    $length = strlen($match[0][self::MATCH_STRING]);

                    if ($length === 0) {
                        continue;
                    }

                    $subjectsFromCallback = $callback($class, $match, $subject);
                    $pos = $match[0][self::MATCH_POSITION];

                    if (count($subjectsFromCallback)) {
                        array_push($newSubjects, $subjectsFromCallback);
                    }

                    if ($pos !== $offset) {
                        $newSubjects[] = $subject->getSubSubject($offset, $pos - $offset);
                    }

                    $offset = $pos + $length;
                }

                if ($offset !== strlen($subject->getString())) {
                    $newSubjects[] = $subject->getSubSubject($offset);
                }
            }

            $subjects = $newSubjects;
        }

        return $subjects;
    }

    /**
     * @param string[][]|int[][] $matches
     * @return string[]
     */
    private function getArguments(array $matches): array
    {
        unset($matches[0]);

        return array_column($matches, self::MATCH_STRING);
    }

    /**
     * @param string $string
     * @param string $replacement
     * @param int $start
     * @param int $length
     * @param array $replaces
     * @return string
     */
    private function replace(string $string, string $replacement, int $start, int $length, array $replaces): string
    {
        $realStart = $start;//pos in current string
            
        foreach ($replaces as $replace) {
            //if ()
        }
        
        
        
        substr_replace('','','','');
    }
    
    private function fill(&$matrix)
    {
        
    }


    private function isIntersect(array $values)
    {

    }

    /**
     * @param string $string
     * @return AbstractTemplate[]
     * @throws Exception
     */
    private function getStructure(string $string): array
    {
        $stringParser = (new StringToPart($string))->createSub($this->valuePatterns);
        $parts = $stringParser->getParts();
        $result = [];
        /** @var TemplatePart[] $otherLevels */
        $otherLevels = $levels = array_fill(0, strlen($string), null);
        $structures = [];
        $map = array_fill(0, strlen($string), true);

        foreach ($parts as $value) {
            $structures[] = $levels[$value->getFrom()] = $value;

            for ($i = $value->getFrom(); $i < $value->getTo(); $i++) {
                $map[$i] = false;
            }
        }

        do {
            $stringParser = $stringParser->createSub($this->structurePatterns);
            $parts = $stringParser->getParts();

            foreach ($parts as $value) {
                for ($i = $value->getFrom(); $i < $value->getTo(); $i++) {
                    if (!isset($otherLevels[$i])) {
                        $otherLevels[$i] = $value;
                    }

                    if (isset($levels[$i])) {
                        $value->addChild($levels[$i]);
                        $levels[$i] = null;
                    }
                }

                $value->getArguments();

                $structures[] = $levels[$value->getFrom()] = $value;
                $map[$value->getFrom()] = $map[$value->getTo()] = 1;
            }
        } while (count($parts));

        $part = '';
        $value = null;

        foreach ($map as $pos => $value) {
            if (!$value) {
                
            }
            if (!isset($otherLevels[$pos])) {
                if ($part !== '') {
                    if ($otherLevels[$pos] === $otherLevels[$pos - 1]) {
                        $part .= $string[$pos];
                    }
                    else {
                        $otherLevels[$pos]->addChild(new TemplatePart(
                            'default',
                            $pos = strlen($part),
                            $pos,
                            [$part]
                        ));
                    }
                }
                else {

                }


            }
        }



        $stringParser = $stringParser->createSub();

        $stringParser->getString();

        foreach ($otherLevels as $value) {

        }
        
        do {
            $count = count($result);
            $subjects = $this->parseByTemplates(
                $this->structurePatterns,
                $subjects,
                function (string $class, array $matches, Subject $subject) use(&$result) {
                    $result[$matches[0][self::MATCH_POSITION]] = [
                        new StructureFactory($class, $this->getArguments($matches)),
                        strlen($matches[0][self::MATCH_STRING])
                    ];
                    
                    /** @var StructureInterface $class */

                    return empty($matches[1][self::MATCH_STRING]) ? []
                        : $subject->getSubSubject(
                            $matches[$class::INCLUDE_NUMBER][self::MATCH_POSITION],
                            strlen($matches[$class::INCLUDE_NUMBER][self::MATCH_STRING])
                        );
                }
            );
        } while ($count !== count($result));

        $this->parseByTemplates(
            $this->valuePatterns,
            $subjects,
            function (string $class, array $matches) use(&$result) {
                $result[$matches[0][self::MATCH_POSITION]] = [
                    (new \ReflectionClass($class))
                        ->newInstanceArgs($this->getArguments($matches)),
                    strlen($matches[0][self::MATCH_STRING])
                ];
            }
        );

        return $result;
    }

    /**
     * @param StructureFactory[]|AbstractTemplate[] $structure
     * @param int $from
     * @param int|null $count
     * @return AbstractTemplate[]
     * @throws \ReflectionException
     */
    private function compileStructure(array $structure, int $count, int $from = 0): array
    {
        $result = [];

        for ($i = $from; $i < $from + $count; $i++) {
            if (!isset($structure[$i])) {
                $i++;

                continue;
            }

            list($object, $length) = $structure[$i];

            $result[] = $object instanceof StructureFactory
                ? $object->createStructure(
                    $this->compileStructure($structure, $length, $i + 1)
                ) : $object;
            $i += $length;
        }

        return $result;
    }
}
