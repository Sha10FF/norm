<?php

namespace NORM;

use ArrayAccess;
use JsonSerializable;
use NORM\SQL\Expression;
use NORM\SQL\SqlFactory;
use PDO;
use PDOStatement;

abstract class Table implements JsonSerializable, ArrayAccess
{
    use Arrayable, DirectAccess;

    /** @var string */
    protected static $table = '';

    /** @var array single or composite primary */
    protected static $primaryKey = [];

    /** @var array all columns including pk, "*" in select if empty */
    protected static $columns = [];

    /** @var PDO */
    protected static $pdo = null;

    /** @var string */
    protected static $pdoDriver;

    /** @var array */
    protected $data = [];

    /** @var array */
    protected $loadedData = [];

    /**
     * Database setup
     * same as PDO constructor
     *
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array|null $options
     */
    public static function setPDO(
        string $dsn,
        string $username = null,
        string $password = null,
        array $options = null
    ): void {
        static::$pdoDriver = explode(':', $dsn, 1)[0];
        static::$pdo = new PDO($dsn, $username, $password, $options);
    }

    /**
     * return record by primary key
     *
     * @param array|mixed $pk
     * @return static
     */
    public static function get($pk): ?self
    {
        if (!is_array($pk)) {
            $pk = [$pk];
        }

        $data = array_combine(static::$primaryKey, $pk);
        $res = static::find($data, 1);
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
    public static function find(array $where = [], ?int $limit = null): array
    {
        $query =
            SqlFactory::getSelect(static::$pdoDriver, static::$table, array_keys(static::$columns), $where, $limit);
        $res = static::query($query->build());
        $return = [];
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

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getPrimaryKey(): ?array
    {
        $pk = [];
        if (array_diff(static::$primaryKey, array_keys($this->data))) {
            return null;
        }
        foreach (static::$primaryKey as $key) {
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

    protected static function query(Expression $expression): ?PDOStatement
    {
        $sql = static::$pdo->prepare($expression->getSql());
        foreach ($expression->getParams() as $k => $v) {

            $type = PDO::PARAM_STR;

            if (is_int($v)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($v)) {
                $type = PDO::PARAM_BOOL;
            } elseif (is_null($v)) {
                $type = PDO::PARAM_NULL;
            }

            $sql->bindValue($k, $v, $type);
        }

        return $sql->execute() ? $sql : null;
    }
}
