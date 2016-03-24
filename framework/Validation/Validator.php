<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 15:45
 */

namespace Framework\Validation;

use Framework\DI\Service;


/**
 * Class Validator
 *
 * @package Framework\Validation
 */
class Validator
{
    protected $_model;
    protected $_errors = [];

    /**
     * Validator constructor.
     * Get ActiveRecord object.
     *
     * @param ActiveRecord $model
     */
    public function __construct($model)
    {
        $this->_model = $model;
    }

    /**
     * Validate fields of ActiveRecord object
     *
     * @return bool True if all fields of ActiveRecord object are valid
     */
    public function isValid()
    {
        $final_validation_result = true; // Default behavior if validation rules are absent

        $fields = $this->_model->getFields();
        $all_rules = $this->_model->getRules();

        foreach ($all_rules as $name => $rules) {
            if (array_key_exists($name, $fields)) {
                foreach ($rules as $rule) {
                    $valid = $rule->isValid($fields[$name]);
                    if ($valid == false) {

                        $this->_errors[$name] = ucfirst($name) . ' validation error';
                        $final_validation_result = false;
                    }
                }
            }
        }

        // Save filled post fields in session to show them in renderer and give user a chance to correct them
        if ($final_validation_result == false) {
            Service::get('session')->setPost($this->_model);
        }

        return $final_validation_result;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}