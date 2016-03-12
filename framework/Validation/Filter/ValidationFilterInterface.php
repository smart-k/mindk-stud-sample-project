<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 14:34
 */

namespace Framework\Validation\Filter;


interface ValidationFilterInterface
{
    public function isValid($value);
}