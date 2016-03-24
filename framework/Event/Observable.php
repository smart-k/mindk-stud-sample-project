<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.03.2016
 * Time: 12:42
 */

namespace Framework\Event;


class Observable
{
    /**
     * @var array   Handlers pool
     */
    protected $handlers = array();

    /**
     * Register new handler for an event
     *
     * @param $event_type
     * @param $handler_class
     */
    public function addHandler($event_type, $handler_class)
    {

        $this->handlers[$event_type][] = $handler_class;
    }

    /**
     * Initiates the process of handling the event
     *
     * @param $event_name
     * @param $subject
     */
    public function triggerEvent($event_name, $subject)
    {

        if (!empty($this->handlers[$event_name])) {

            $event = new Event($event_name, $subject);
            $handlers = $this->handlers[$event_name];

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