<?php

namespace NORM\SQL\Common;

use NORM\SQL\Expression;
use NORM\SQL\Query;
use NORM\SQL\Syntax;

class Delete implements Query
{
    /** @var string */
    protected $table = '';

    /** @var array */
    protected $where = [];

    /** @var ?int */
    protected $limit = null;

    /** @var Syntax */
    protected $syntax;

    public function __construct(string $table, array $where, ?int $limit)
    {
        $this->table = $table;
        $this->where = $where;
        $this->limit = $limit;
        $this->syntax = new CommonSyntax();
    }

    public function setSyntax(Syntax $syntax): void
    {
        $this->syntax = $syntax;
    }

    public function build(): Expression
    {
        $params = [];
        $sql = 'DELETE FROM ' . $this->syntax->table($this->table);
        $sql .= ' WHERE ';

        $where = [];
        foreach ($this->where as $key => $val) {
            $param = ':' . $key;
            $where[] = $this->syntax->col($key) . '=' . $param;
            $params[$param] = $val;
        }
        if (empty($where)) {
            $where[] = 'TRUE';
        }

        $sql .= implode(' AND ', $where);

        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return new Expression($sql, $params);
    }
}