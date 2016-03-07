<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 07.03.2016
 * Time: 8:41
 */

namespace Framework\Model;


abstract class ActiveRecord
{
    protected static $db = null;

    public static function getDBCon()
    {

        if (empty(self::$db)) {
            self::$db = Service::get('db');
        }

        return self::$db;
    }

    public abstract function getTable();

    public static function find($mode = 'all')
    {

        $table = static::getTable();

        $sql = "SELECT * FROM " . $table;

        if (is_numeric($mode)) {
            $sql .= " WHERE id=" . (int)$mode;
        }

        // PDO request...

        return $result;
    }

    protected function getFields()
    {

        return get_object_vars($this);
    }

    public function save()
    {
        $fields = $this->getFields();

        // @TODO: build SQL expression, execute
    }
}