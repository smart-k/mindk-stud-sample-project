<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.03.2016
 * Time: 12:42
 */

namespace Framework\Event;

use Framework\ObjectPool;

/**
 * Class Observable
 *
 * @package Framework\Event
 */
class Observable extends ObjectPool
{
    /**
     * @var array The event's handlers pool
     */
    protected $_handlers = [];

    /**
     * Register new handler for an event
     *
     * @param string $event_type The event's type
     * @param string $handler_class The event's handler class
     */
    public function addHandler($event_type, $handler_class)
    {
        $this->_handlers[$event_type][] = $handler_class;
    }

    /**
     * Initiate the process of handling the event
     *
     * @param string $event_type The event's type
     * @param object $subject The event's data
     */
    public function triggerEvent($event_type, $subject)
    {
        if (!empty($this->_handlers[$event_type])) {

            $event = new Event($event_type, $subject);
            $handlers = $this->_handlers[$event_type];

            do {
                $handler_class = array_shift($handlers);

                if (class_exists($handler_class)) {
                    $handler = new $handler_class();
                    $handler->handle($event);
                }

            } while (!empty($handlers) && !$event->isStopped());
        }
    }
}