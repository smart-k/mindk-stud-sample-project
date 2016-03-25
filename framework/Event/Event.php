<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.03.2016
 * Time: 12:36
 */

namespace Framework\Event;


/**
 * Class Event
 *
 * @package Framework\Event
 */
class Event
{
    /**
     * @var string The event's type
     */
    public $type;

    /**
     * @var null|object The event's data
     */
    protected $_subject;

    /**
     * @var bool True if stop event propagation
     */
    protected $_stopped = false;

    /**
     * Event class constructor
     *
     * @param string $event_type
     * @param null|object $subject
     */
    public function __construct($event_type, $subject = null)
    {
        $this->type = $event_type;
        $this->_subject = $subject;
    }

    /**
     * Stop event propagation
     */
    public function stopPropagation()
    {
        $this->_stopped = true;
    }

    /**
     * Detect event's status
     */
    public function isStopped()
    {
        return $this->_stopped;
    }

    /**
     * Get event's data
     */
    public function getSubject()
    {
        return $this->_subject;
    }

}