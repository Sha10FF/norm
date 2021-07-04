<?php

namespace NORM;

trait Arrayable
{
    protected $data = [];

    public function offsetGet($name)
    {
        return $this->data[$name];
    }

    public function offsetSet($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function offsetExists($name): bool
    {
        return isset($this->data[$name]);
    }

    public function offsetUnset($name): void
    {
        unset($this->data['name']);
    }
}
