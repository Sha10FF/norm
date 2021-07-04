<?php

namespace NORM\SQL\Common;

use NORM\SQL\Expression;

class Select extends Syntax
{
    /** @var string */
    protected $table = '';

    /** @var array */
    protected $where = [];

    /** @var array */
    protected $fields = [];

    /** @var ?int */
    protected $limit = null;

    public function __construct(string $table, array $where = [], array $fields = [], ?int $limit = null)
    {
        $this->table = $table;
        $this->where = $where;
        $this->fields = $fields;
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @param array $where
     */
    public function setWhere(array $where): void
    {
        $this->where = $where;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function build(): Expression
    {
        $params = [];
        $sql = 'SELECT ';
        if ($this->fields) {
            $fields = [];
            foreach ($this->fields as $field) {
                $fields[] = self::FIELD_MARKER_BEGIN . $field . self::FIELD_MARKER_END;
            }
            $sql .= implode(self::FIELD_SEPARATOR, $fields);
        } else {
            $sql .= '*';
        }
        $sql .= ' FROM ' . self::TABLE_MARKER_BEGIN . $this->table . self::TABLE_MARKER_END;
        $sql .= ' WHERE ';

        $where = [];
        foreach ($this->where as $key => $val) {
            $param = self::PARAM_MARKER_BEGIN . $key . self::PARAM_MARKER_END;
            $where[] = self::FIELD_MARKER_BEGIN . $key . self::FIELD_MARKER_END . '=' . $param;
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