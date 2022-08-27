<?php namespace EugeneErg\Preparer;

trait ToStringAsHash
{
    private ?string $hash = null;

    public function __toString(): string
    {
        if ($this->hash === null) {
            /** @var Hasher $hasher */
            $hasher = ClassCreatorService::instance()
                ->createSingle(Hasher::class);
            $this->hash = $hasher->getHash($this);
        }

        return $this->hash;
    }
}