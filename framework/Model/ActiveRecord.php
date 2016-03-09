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
    public static function find($mode = 'all')
    {
        $db = Service::get('db');
        $table = static::getTable();
        $sql = "SELECT * FROM " . $table;

        if (is_numeric($mode)) {
            $sql .= " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute(array((int)$mode));
        } else {
            $stmt = $db->query($sql);
        }
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        $output = (is_numeric($mode)) ? $result[0] : $result;
        return $output;
    }

    /**
     * Get names of database table fields
     * @return assoc array
     */
    protected function _getFields()
    {

        return get_object_vars($this);
    }

    /**
     * Insert data into database table
     */
    public function save()
    {
        $set = '';
        $values = array(); // Assoc array for PDO::prepare()
        $db = Service::get('db');
        $table = static::getTable();
        $fields = $this->_getFields();

        foreach ($fields as $key => $value) {
            if (isset($key)) {
                $set .= "`" . str_replace("`", "``", $key) . "`" . "=:$key, ";
                $values[":$key"] = $value;
            }
        }
        $set = substr($set, 0, -2);

        $sql = "INSERT INTO " . $table . "SET " . $set;
        $stmt = $db->prepare($sql);

        return $stmt->execute($values);
    }
}