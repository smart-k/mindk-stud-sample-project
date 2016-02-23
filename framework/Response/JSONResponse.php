<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 23.02.2016
 * Time: 9:32
 */

namespace Framework\Response;


/**
 * Class JsonResponse
 * Response represents an HTTP response in JSON format.
 *
 * @package Framework\Response
 */
class JsonResponse extends Response
{
   /**
     * JsonResponse constructor.
     * @param array $data The response data
     * @param string $type Content-Type
     * @param int $code The response status code
     */
    public function __construct($data = null, $type = 'application/json', $code = 200)
    {
        parent::__construct('', $type, $code);
        if ($data === null) {
            $data = new \ArrayObject();
        }
        $this->content = json_encode($data);
    }
}