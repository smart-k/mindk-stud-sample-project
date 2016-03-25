<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 12.03.2016
 * Time: 14:31
 */

namespace Framework\Validation\Filter;


/**
 * Class Length
 *
 * @package Framework\Validation\Filter
 */
class Length implements ValidationFilterInterface
{
    protected $min;
    protected $max;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function isValid($value)
    {
        return (strlen($value) >= $this->min) && (strlen($value) <= $this->max);
    }
}