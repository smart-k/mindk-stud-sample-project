<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.03.2016
 * Time: 12:39
 */

namespace Framework\Event;


interface EventHandler
{
    function handle(Event &$event);
}