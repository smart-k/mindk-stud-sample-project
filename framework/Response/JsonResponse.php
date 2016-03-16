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
 * JsonResponse represents an HTTP response in JSON format.
 *
 * @package Framework\Response
 */
class JsonResponse extends Response
{
    /**
     * JsonResponse constructor.
     *
     * @param array $data The response data
     * @param int $code The response status code
     * @param string $type Content-Type
     */
    public function __construct(Array $data = null, $code = 200, $type = 'application/json')
    {
        parent::__construct('', $code, $type);
        if ($data === null) {
            $this->code = 500;
            $data = new \ArrayObject();
        }
        $this->content = json_encode($data);
    }
}