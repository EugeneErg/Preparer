<?php namespace EugeneErg\Preparer\SQL\Query;

class ReturningQuery
{
    private AbstractSource $data;
    private array $select;

    public function __construct(AbstractSource $data, array $select = [])
    {
        $this->data = $data;
        $this->select = $select;
    }

    public function getData(): AbstractSource
    {
        return $this->data;
    }

    public function getSelect(): array
    {
        return $this->select;
    }
}
