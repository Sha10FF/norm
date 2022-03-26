<?php

namespace NORM\SQL\Common;

use NORM\SQL\Expression;
use NORM\SQL\Query;
use NORM\SQL\Syntax;

class Insert implements Query
{
    /** @var string */
    protected $table = '';

    /** @var array */
    protected $data = [];

    /** @var Syntax */
    protected $syntax;

    public function __construct(string $table, array $data)
    {
        $this->table = $table;
        $this->data = $data;
        $this->syntax = new CommonSyntax();
    }

    public function setSyntax(Syntax $syntax): void
    {
        $this->syntax = $syntax;
    }

    public function build(): Expression
    {
        $params = [];
        $cols = [];
        $values = [];

        foreach ($this->data as $field => $data) {
            $cols[] = $this->syntax->col($field);
            $values[] = ':' . $field;
            $params[':' . $field] = $data;
        }

        $sql =
            'INSERT INTO ' . $this->syntax->table($this->table) . ' (';
        $sql .= implode(', ', $cols);
        $sql .= ') VALUES (';
        $sql .= implode(', ', $values);
        $sql .= ')';

        return new Expression($sql, $params);
    }
}