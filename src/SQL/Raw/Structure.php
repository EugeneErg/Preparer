<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\File;

final class Structure
{
    /** @var Structure[] */
    private array $children = [];

    public function __construct(array $tree)
    {
        $this->createBranches($tree);
    }

    private function createBranches(array $tree, array $path = [''], array &$branches = []): void
    {
        $stringPath = implode('/', $path);
        $branches[$stringPath] = $this;

        foreach (array_reverse($tree) as $name => $config) {
            if (is_array($config)) {
                $childPath = $path;
                $childPath[] = $name;
                $this->children[$name] = $this->getBranchAndUpdate($stringPath . '/' . $name, $branches);
                $this->children[$name]->createBranches($config, $childPath, $branches);
            } else {
                $validPath = $this->getAbsolutePath($path, $config);
                $this->children[is_numeric($name) ? end($validPath) : $name] = $this->getBranchAndUpdate(
                    count($validPath) > 1 ? implode('/', $validPath) : $stringPath . '/' . $name,
                    $branches
                );
            }
        }
    }

    public function findChild(string $name): ?self
    {
        foreach ($this->children as $childName => $child) {
            if (preg_match('/^' . str_replace('/', '\\/', $childName) . '$/', $name) !== 0) {
                return $child;
            }
        }

        return null;
    }

    private function getAbsolutePath(array $fullPath, string $currentPath): array
    {
        $currentPath = explode('/', $currentPath);

        if ($currentPath[0] === '') {
            return $currentPath;
        }

        $file = File::ofPath(implode('/', $fullPath) . '/' . $currentPath);

        return explode('/', $file->getRealPath());
    }

    private function getBranchAndUpdate(string $string, array &$branches): self
    {
        if (!isset($branches[$string])) {
            $branches[$string] = new self([]);
        }

        return $branches[$string];
    }
}