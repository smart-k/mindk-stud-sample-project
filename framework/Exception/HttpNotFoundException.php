<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.02.2016
 * Time: 14:51
 */

namespace Framework\Exception;


/**
 * Class HttpNotFoundException
 *
 * @package Framework\Exception
 */
class HttpNotFoundException extends \Exception
{
    protected $code = 404;
}