<?php

namespace NORM\SQL;

interface Syntax
{
    public function table(string $tableName): string;

    public function col(string $colName): string;
}
