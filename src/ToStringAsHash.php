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

    public static function getByHash(string $hash): self
    {
        /** @var Hasher $hasher */
        $hasher = ClassCreatorService::instance()
            ->createSingle(Hasher::class);
        /** @var self $result */
        $result = $hasher->getObject($hash);

        return $result;
    }
}