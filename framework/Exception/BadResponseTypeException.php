<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.02.2016
 * Time: 14:47
 */

namespace Framework\Exception;


/**
 * Class BadResponseTypeException
 *
 * @package Framework\Exception
 */
class BadResponseTypeException extends \Exception
{
    protected $code = 500;
}