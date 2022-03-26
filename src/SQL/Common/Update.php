<?php

namespace NORM\SQL\Common;

use NORM\SQL\Expression;
use NORM\SQL\Query;
use NORM\SQL\Syntax;

class Update implements Query
{
    /** @var string */
    protected $table = '';

    /** @var array */
    protected $set = [];

    /** @var array */
    protected $where = [];

    /** @var Syntax */
    protected $syntax;

    public function __construct(string $table, array $set, array $where)
    {
        $this->table = $table;
        $this->where = $where;
        $this->data = $set;
        $this->syntax = new CommonSyntax();
    }

    public function setSyntax(Syntax $syntax): void
    {
        $this->syntax = $syntax;
    }

    public function build(): Expression
    {
        $params = [];
        $sql = 'UPDATE ' . $this->syntax->table($this->table) . ' SET ';

        $set = [];
        foreach ($this->set as $key => $val) {
            $param = ':new_' . $key;
            $set[] = $this->syntax->col($key) . ' = ' . $param;
            $params[$param] = $val;
        }
        $sql .= implode(', ', $set);

        $sql .= ' WHERE ';
        $where = [];
        foreach ($this->where as $key => $val) {
            $param = ':old_' . $key;
            $where[] = $this->syntax->col($key) . ' = ' . $param;
            $params[$param] = $val;
        }
        if (empty($where)) {
            $where[] = 'TRUE';
        }
        $sql .= implode(' AND ', $where);

        return new Expression($sql, $params);
    }
}