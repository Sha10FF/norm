<?php

namespace NORM\SQL;

use NORM\SQL\Common\Delete;
use NORM\SQL\Common\Insert;
use NORM\SQL\Common\Select;
use NORM\SQL\Common\Update;

abstract class SqlFactory
{
    public static function getSelect(
        string $pdoDriver,
        string $table,
        array $columns = [],
        array $where = [],
        int $limit = null
    ): Query {
        switch ($pdoDriver) {
            default:
                return new Select($table, $columns, $where, $limit);
        }
    }

    public static function getInsert(
        string $pdoDriver,
        string $table,
        array $data
    ): Query {
        switch ($pdoDriver) {
            default:
                return new Insert($table, $data);
        }
    }

    public static function getUpdate(
        string $pdoDriver,
        string $table,
        array $set,
        array $where
    ): Query {
        switch ($pdoDriver) {
            default:
                return new Update($table, $set, $where);
        }
    }

    public static function getDelete(
        string $pdoDriver,
        string $table,
        array $where,
        int $limit = null
    ): Query {
        switch ($pdoDriver) {
            default:
                return new Delete($table, $where, $limit);
        }
    }
}