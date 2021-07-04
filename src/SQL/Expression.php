<?php

namespace NORM\SQL;

class Expression
{
    protected $sql = '';
    protected $params = [];

    /**
     * Expression constructor.
     *
     * @param string $sql
     * @param array $params
     */
    public function __construct(string $sql, array $params)
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }


}