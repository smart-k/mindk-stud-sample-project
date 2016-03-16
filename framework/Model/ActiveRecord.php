<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 07.03.2016
 * Time: 8:41
 */

namespace Framework\Model;

use Framework\DI\Service;
use Framework\Exception\DatabaseException;

abstract class ActiveRecord
{
    /**
     * Default primary key in database tables
     *
     * @var int
     */
    public $id;

    /**
     * @return array
     */
    public function getRules()
    {
        return [];
    }

    /**
     * Find records in database tables
     * @param string $mode
     * @return mixed $output
     * @throws DatabaseException If database query returns false
     */
    public static function find($mode = 'all')
    {
        $db = Service::get('db');
        $table = static::getTable();
        $sql = "SELECT * FROM " . $table;

        if (is_numeric($mode)) {
            $sql .= " WHERE id = :id";
            $query = $db->prepare($sql);
            $query->bindParam(":id", $mode, \PDO::PARAM_INT, 11);
            $check_query_result = $query->execute();
        } else {
            $sql .= " ORDER BY date";
            $query = $db->query($sql);
            $check_query_result = $query;
        }

        if ($check_query_result === false) {
            $error_code = is_numeric($mode) ? $query->errorCode() : $db->errorCode();
            throw new DatabaseException('Database reading error: ' . $error_code);
        }

        $result = $query->fetchAll(\PDO::FETCH_CLASS, get_called_class());

        if (is_numeric($mode) && isset($result[0])) {
            return $result[0];
        }

        return $result;
    }


    /**
     * Get names of database table fields
     * @return assoc array
     */
    public function _getFields()
    {

        return get_object_vars($this);
    }

    /**
     * Validate and insert data into database table
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

        $sql = "INSERT INTO " . $table . " SET " . $set;

        $stmt = $db->prepare($sql);
        $result = $stmt->execute($values);

        $class = get_called_class();
        if ($class === 'Blog\Model\User' && $result == true) {
            Service::get('security')->setUser($this); // If $route['pattern'] == '/signin'
        }
        return $result;
    }
}