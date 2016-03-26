<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.03.2016
 * Time: 18:45
 */

namespace Framework\Event\SetDBNamesHandler;

use Framework\Event\Event;
use Framework\Event\EventHandler\EventHandler;
use Framework\Exception\DatabaseException;

/**
 * Class SetDBNamesHandler
 * Set database names
 *
 * @package Framework\Event\SetDBNamesHandler
 */
class SetNamesDBHandler implements EventHandler
{
    /**
     * Set database names
     *
     * @param Event $event
     *
     * @throws DatabaseException If database query returns false
     */
    public function handle(Event &$event)
    {
        $db = $event->getSubject();
        $sql = "SET NAMES utf8";
        if ($db->query($sql) === false) {
            throw new DatabaseException('Database writing error: ' . $db->errorCode());
        }
    }
}