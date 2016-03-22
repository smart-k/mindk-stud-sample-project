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

/**
 * Class ActiveRecord
 *
 * @package Framework\Model
 */
abstract class ActiveRecord
{
    /**
     * Get validation rules
     * @return array
     */
    public function getRules()
    {
        return [];
    }

    /**
     * Find records in database tables
     *
     * @param string $mode Search condition
     * @param mixed|null $value Database table field value
     *
     * @return object|null $output
     * @throws DatabaseException If database query returns false
     */
    public static function find($mode = 'all', $value = null)
    {
        $db = Service::get('db');
        $table = static::getTable();
        $sql = "SELECT * FROM " . $table;

        if ($mode === 'all') { // Select all records from the database table
            $sql .= " ORDER BY date";
            $stmt = $db->query($sql);
            $check_query_result = $stmt;
        } elseif (is_numeric($mode)) { // Select record from the database table with specified ID
            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $mode);
            $check_query_result = $stmt->execute();
        } elseif (isset($value)) { // Select record from the database table with specified field=>value
            $field_check_query = "SHOW COLUMNS FROM " . $table . " WHERE FIELD = ?";
            $stmt = $db->prepare($field_check_query);
            $stmt->execute(array($mode));
            $check = $stmt->fetchColumn();

            if ($check === false) {
                throw new DatabaseException("Database reading error. Table '{$table}' does not have the field '{$mode}'");
            }

            $sql .= " WHERE {$mode} = :value";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":value", $value);
            $check_query_result = $stmt->execute();
        } else {
            return null;
        }

        if ($check_query_result === false) {
            $error_code = is_numeric($mode) ? $stmt->errorCode() : $db->errorCode();
            throw new DatabaseException('Database reading error: ' . $error_code);
        }

        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());

        if ($mode === 'all') {
            return $result;
        }

        if (is_numeric($mode) && isset($result[0])) {
            return $result[0];
        }

        return !empty($result) ? $result[0] : null;

    }

    public static function __callStatic($method, $arguments)
    {
        $preg_template = '~^findBy~i';

        if (!empty($field = preg_split($preg_template, $method)[1])) {
            $field = strtolower($field); // Get database field name
            return static::find($field, (string)$arguments[0]);
        }
    }

    /**
     * Get names of database table fields
     *
     * @return assoc array
     */
    public function getFields()
    {
        return get_object_vars($this);
    }

    /**
     * Validate and insert data into database table
     */
    public function save()
    {
        $set = '';
        $values = []; // Assoc array for PDO::prepare()

        $db = Service::get('db');
        $table = static::getTable();
        $fields = $this->getFields();


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

        if ($result === false) {
            throw new DatabaseException('Database writing error: ' . $stmt->errorCode());
        }

        return $result;
    }


}