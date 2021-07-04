<?php

namespace NORM;

trait DirectAccess
{
    protected $data = [];

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset($name): bool
    {
        return isset($this->data[$name]);
    }

    public function __unset($name): void
    {
        unset($this->data['name']);
    }
}
