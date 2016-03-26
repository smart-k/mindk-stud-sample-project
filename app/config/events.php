<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 24.03.2016
 * Time: 13:42
 */

return array(
    'app.init' => array('Framework\\Event\\EventHandler\\SetNamesDBHandler'),
    'app.exit' => array('Framework\\Event\\EventHandler\\DisconnectDBHandler')
);