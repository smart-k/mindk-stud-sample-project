<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 08.03.2016
 * Time: 20:22
 */

namespace Framework\Request;


class Request {
    /**
     * Получить путь запроса без строки запроса и имени выполняемого
     * скрипта
     *
     * @return string
     */
    public function getPathInfo() {
        // получаем значения:
        //
        // - URI без имени хоста
        // - строки запроса после ?
        // - имя выполняемого скрипта
        $request_uri = $_SERVER['REQUEST_URI'];
        $query_string = $_SERVER['QUERY_STRING'];
        $script_name = $_SERVER['SCRIPT_NAME'];

        // извлекаем из URI путь запроса,
        $path_info = parse_url($_SERVER['REQUEST_URI'])['path'];
        // возвращаем результат
        return empty($path_info) ? '/' : $path_info;
    }

    /**
     * Поиск и получение значения параметра зпроса
     * по ключу
     *
     * @param string $key               искомый ключ параметра запроса
     * @return mixed                    значение параметра
     *                                  или null если параметр не существует
     */
    public function find($key) {
        if ( key_exists($key, $_REQUEST) )
            return $_REQUEST[$key];
        else
            return null;
    }

    /**
     * Проверяет существование параметра в запросе
     * по его ключу
     *
     * @param string $key               проверяемый ключ
     * @return boolean
     */
    public function has($key)
    {
        return key_exists($key, $_REQUEST);
    }
}