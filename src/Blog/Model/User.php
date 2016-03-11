<?php
/**
 * Created by PhpStorm.
 * User: dgilan
 * Date: 10/17/14
 * Time: 12:09 PM
 */

namespace Blog\Model;

use Framework\Model\ActiveRecord;
use Framework\Security\Model\UserInterface;
use Framework\DI\Service;

class User extends ActiveRecord implements UserInterface
{
    public $id;
    public $email;
    public $password;
    public $role;

    public static function getTable()
    {
        return 'users';
    }

    public function getRole()
    {
        return $this->role;
    }

    /**
     * Find records in database table by email attribute
     *
     * @param $email
     * @return mixed
     */
    public static function findByEmail($email)
    {
        $db = Service::get('db');
        $table = static::getTable();
        $sql = "SELECT * FROM " . $table . " WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array((string)$email));
        $result = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        return $result[0];
    }

}