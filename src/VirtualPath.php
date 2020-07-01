<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Action\AbstractAction;

/**
 * Class VirtualPath
 * @package EugeneErg\Preparer
 */
class VirtualPath
{
    /**
     * @var AbstractVirtualType
     */
    private $value;

    /**
     * @var array
     */
    private $path;

    /**
     * @var string|null
     */
    private $resultType;

    /**
     * VirtualPath constructor.
     * @param AbstractVirtualType $value
     * @param AbstractAction[] $path
     * @param string|null $resultType
     */
    public function __construct(AbstractVirtualType $value, array $path, string $resultType = null)
    {
        $this->value = $value;
        $this->path = $path;
        $this->resultType = $resultType;
    }

    /**
     * @return AbstractVirtualType
     */
    public function getValue(): AbstractVirtualType
    {
        return $this->value;
    }

    /**
     * @return AbstractAction[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getResultType(): ?string
    {
        return $this->resultType;
    }
}
