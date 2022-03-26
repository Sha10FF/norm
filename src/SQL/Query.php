<?php

namespace NORM\SQL;

interface Query
{
    public function build(): Expression;

    public function setSyntax(Syntax $syntax): void;
}