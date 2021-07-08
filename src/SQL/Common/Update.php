<?php

namespace NORM\SQL\Common;

use NORM\SQL\Expression;

class Update extends Query
{
    /** @var string */
    protected $table = '';

    /** @var array */
    protected $set = [];

    /** @var array */
    protected $where = [];

    public function __construct(string $table, array $set = [], array $where = [])
    {
        $this->table = $table;
        $this->where = $where;
        $this->data = $set;
    }

    public function build(): Expression
    {
        $params = [];
        $sql = 'UPDATE '. self::TABLE_MARKER_BEGIN . $this->table . self::TABLE_MARKER_END . ' SET ';

        $set = [];
        foreach ($this->set as $key => $val) {
            $param = ':new_' . $key;
            $set[] = self::COLS_MARKER_BEGIN . $key . self::COLS_MARKER_END . ' = ' . $param;
            $params[$param] = $val;
        }
        $sql .= implode(', ', $set);

        $sql .= ' WHERE ';
        $where = [];
        foreach ($this->where as $key => $val) {
            $param = ':old_' . $key;
            $where[] = self::COLS_MARKER_BEGIN . $key . self::COLS_MARKER_END . ' = ' . $param;
            $params[$param] = $val;
        }
        if (empty($where)) {
            $where[] = 'TRUE';
        }
        $sql .= implode(' AND ', $where);

        return new Expression($sql, $params);
    }
}