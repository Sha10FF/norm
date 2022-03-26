<?php

namespace NORM\SQL;

class Expression
{
    /** @var string */
    protected $sql = '';

    /** @var array */
    protected $params = [];

    public function __construct(string $sql, array $params)
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}