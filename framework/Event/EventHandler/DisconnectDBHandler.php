<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 25.03.2016
 * Time: 11:24
 */

namespace Framework\Event\EventHandler\DisconnectDBHandler;

use Framework\Event\Event;
use Framework\Event\EventHandler\EventHandler;

/**
 * Class DisconnectDBHandler
 *
 * @package Framework\Event\EventHandler\DisconnectDBHandler
 */
class DisconnectDBHandler implements EventHandler
{
    /**
     * Disconnect database
     *
     * @param Event $event
     */
    public function handle(Event &$event)
    {
        $event->getSubject()->this = null;
    }
}