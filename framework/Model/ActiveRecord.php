<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 07.03.2016
 * Time: 8:41
 */

namespace Framework\Model;

use Framework\DI\Service;


abstract class ActiveRecord
{
    /**
     * Default primary key in database tables
     *
     * @var int
     */
    public $id;

    /**
     * Find records in database tables
     * @param string $mode
     * @return mixed $output
     */
    static function find($mode = 'all')
    {
        $table = static::getTable();

        $sql = "SELECT * FROM " . $table;

        if (is_numeric($mode)) {
            $sql .= " WHERE id=" . (int)$mode;
        }
        // PDO request...
        $result = Service::get('db')->query($sql)->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        $output = (is_numeric($mode)) ? $result[0] : $result;
        return $output;
    }

    protected function getFields()
    {

        return get_object_vars($this);
    }

    function save()
    {
        $fields = $this->getFields();

        // @TODO: build SQL expression, execute
    }
}