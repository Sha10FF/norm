<?php

namespace NORM\SQL\Common;

use NORM\SQL\Syntax;

class CommonSyntax implements Syntax
{
    public function table(string $tableName): string
    {
        return '`' . $tableName . '`';
    }

    public function col(string $colName): string
    {
        return '`' . $colName . '`';
    }
}