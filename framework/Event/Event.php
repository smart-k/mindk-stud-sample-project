<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.03.2016
 * Time: 12:36
 */

namespace Framework\Event;


class Event
{
    public $type;
    protected $subject;
    protected $stopped = false;

    /**
     * Event class constructor
     *
     * @param $event_type
     * @param null $subject
     */
    public function __construct($event_type, $subject = null)
    {

        $this->type = $event_type;
        $this->subject = $subject;
    }

    /**
     * Event stop
     */
    public function stopPropagation()
    {

        $this->stopped = true;
    }

    /**
     * Detects event's status
     */
    public function isStopped()
    {

        return $this->stopped;
    }

    /**
     * Get event data
     */
    public function getSubject()
    {
        return $this->subject;
    }

}