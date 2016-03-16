<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 14:33
 */

namespace Framework\Validation\Filter;


/**
 * Class NotBlank
 *
 * @package Framework\Validation\Filter
 */
class NotBlank implements ValidationFilterInterface
{
    public function isValid($value)
    {
        return !empty($value);
    }
}