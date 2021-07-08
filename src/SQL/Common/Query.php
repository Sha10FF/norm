<?php

namespace NORM\SQL\Common;

abstract class Query
{
    protected const TABLE_MARKER_BEGIN = '`';
    protected const TABLE_MARKER_END = '`';
    protected const COLS_MARKER_BEGIN = '`';
    protected const COLS_MARKER_END = '`';
    protected const COLS_SEPARATOR = ',';
}