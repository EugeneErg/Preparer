<?php namespace EugeneErg\Preparer;

class File
{
    private ?string $path = null;

    private function __construct()
    {
    }

    public static function ofPath(string $path): self
    {
        $result = new self();
        $result->path = $path;

        return $result;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getRealPath(): ?string
    {
        if ($this->path === null) {
            return null;
        }

        $parts = array_filter(explode('/', $this->path), 'strlen');
        $absolutes = array();

        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }

            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode('/', $absolutes);
    }
}
