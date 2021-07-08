<?php

namespace NORM\SQL;

use NORM\SQL\Common\Select;

abstract class SqlFactory
{
    public static function getSelect(string $pdoDriver, string $table, $columns = [], $where = [], $limit = null)
    {
        switch ($pdoDriver) {
            default:
                return new Select($table, $columns, $where, $limit);
        }
    }


}