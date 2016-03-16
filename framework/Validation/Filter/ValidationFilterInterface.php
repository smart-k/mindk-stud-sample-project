<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 14:34
 */

namespace Framework\Validation\Filter;


/**
 * Interface ValidationFilterInterface
 *
 * @package Framework\Validation\Filter
 */
interface ValidationFilterInterface
{
    /**
     * Check if valid value
     *
     * @param mixed $value
     * @return bool True if valid value
     */
    public function isValid($value);
}