<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 17.02.2016
 * Time: 15:08
 */

namespace Framework;


class ReflectionMethodAssocParams extends \ReflectionMethod
{
    public function invokeArgs($object, Array $args = array())
    {
        $parameters = $this->getParameters();
        foreach ($parameters as &$param) {
            $name = $param->getName();
            $param = !empty($args[$name]) ? $args[$name] : $param->getDefaultValue();
        }
        unset($param);

        return parent::invokeArgs($object, $parameters);
    }
}