<?php

namespace NORM;

use ArrayAccess;
use JsonSerializable;
use NORM\SQL\Common\Select;
use NORM\SQL\Common\Update;
use NORM\SQL\Expression;
use PDO;
use PDOStatement;

abstract class Table implements JsonSerializable, ArrayAccess
{
    use Arrayable, DirectAccess;

    /** @var string */
    protected static $table = '';

    /** @var array single or composite cols */
    protected static $pk = [];

    /** @var array all cols including pk, "*" in select if empty */
    protected static $cols = [];

    /** @var PDO */
    protected static $pdo = null;

    /** @var array */
    protected $data = [];

    /** @var array */
    protected $loadedData = [];

    public static function setPDO(PDO $pdo): void
    {
        static::$pdo = $pdo;
    }

    /**
     * @param array|mixed $pk
     * @return static
     */
    public static function get($pk): ?self
    {
        $data = [];
        if (!is_array($pk)) {
            $pk = [$pk];
        }
        $data = array_combine(static::$pk, $pk);
        $res = self::find($data, 1);
        if (!empty($res)) {
            return $res[0];
        }
        return null;
    }

    /**
     * @param array $data
     * @param int|null $limit
     * @return static[]
     */
    public static function find(array $data = [], ?int $limit = null): array
    {
        $query = new Select(static::$table, self::$cols, $data, $limit);
        $return = [];

        $res = self::query($query->build());

        if ($res) {
            while ($item = $res->fetch(PDO::FETCH_ASSOC)) {
                $o = new static($item);
                $o->loadedData = $item;
                $return[] = $o;
            }
            $res->closeCursor();
        }

        return $return;
    }

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function getPk(): ?array
    {
        $pk = [];
        if (array_diff(static::$pk, array_keys($this->data))) {
            return null;
        }
        foreach (static::$pk as $key) {
            $pk[] = $this->data[$key];
        }

        return $pk;
    }

    public function isNew(): bool
    {
        return empty($this->loadedData);
    }

    public function getChanged(): array
    {
        return array_diff_assoc($this->data, $this->loadedData);
    }

    public function isChanged(): bool
    {
        return !empty($this->getChanged());
    }

    public function jsonSerialize()
    {
        return $this->data;
    }

    public function save(): bool
    {
        return false;
    }

    public function insert(): bool
    {
        return false;
    }

    protected static function query(Expression $expression): ?PDOStatement
    {
        $sql = static::$pdo->prepare($expression->getSql());
        foreach ($expression->getParams() as $k => $v) {

            $type = PDO::PARAM_STR;

            if (is_int($v)) {
                $type = PDO::PARAM_INT;
            }
            if (is_bool($v)) {
                $type = PDO::PARAM_BOOL;
            }
            if (is_null($v)) {
                $type = PDO::PARAM_NULL;
            }

            $sql->bindValue($k, $v, $type);
        }

        return $sql->execute() ? $sql : null;
    }
}
