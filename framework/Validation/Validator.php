<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 15:45
 */

namespace Framework\Validation;


class Validator
{
    protected $_model;
    protected $_errors = array();

    /**
     * Validator constructor.
     * Get ActiveRecord object.
     */
    public function __construct($model)
    {
        $this->_model = $model;
    }

    /**
     * Validate fields of ActiveRecord object
     */
    public function isValid()
    {
        $validation_result = false;

        $fields = $this->_model->_getFields();
        $all_rules = $this->_model->getRules();

        foreach ($all_rules as $name => $rules) {
            if (array_key_exists($name, $fields)) {
                foreach ($rules as $rule) {
                    $valid = $rule->isValid($fields[$name]);
                    if ($valid == false) {
                        $this->_errors[$name] = ucfirst($name) . ' validation error';
                    } else {
                        $validation_result = true;
                    }
                }
            }
        }
        return $validation_result;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}