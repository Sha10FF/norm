<?php

namespace NORM\SQL\Common;

use NORM\SQL\Expression;

class Select extends Query
{
    /** @var string */
    protected $table = '';

    /** @var array */
    protected $cols = [];

    /** @var array */
    protected $where = [];

    /** @var ?int */
    protected $limit = null;

    public function __construct(string $table, array $cols, array $where, ?int $limit)
    {
        $this->table = $table;
        $this->cols = $cols;
        $this->where = $where;
        $this->limit = $limit;
    }

    public function build(): Expression
    {
        $params = [];
        $sql = 'SELECT ';
        if ($this->cols) {
            $cols = [];
            foreach ($this->cols as $field) {
                $cols[] = self::COLS_MARKER_BEGIN . $field . self::COLS_MARKER_END;
            }
            $sql .= implode(self::COLS_SEPARATOR, $cols);
        } else {
            $sql .= '*';
        }
        $sql .= ' FROM ' . self::TABLE_MARKER_BEGIN . $this->table . self::TABLE_MARKER_END;
        $sql .= ' WHERE ';

        $where = [];
        foreach ($this->where as $key => $val) {
            $param = ':' . $key;
            $where[] = self::COLS_MARKER_BEGIN . $key . self::COLS_MARKER_END . '=' . $param;
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