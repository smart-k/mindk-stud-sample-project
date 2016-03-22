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
     * @param array|null $data The response data
     * @param int $code The response status code
     * @param string $type Content-Type
     */
    public function __construct(Array $data = [], $code = 200, $type = 'application/json')
    {
        parent::__construct('', $code, $type);
        if (empty($data)) {
            $this->code = 500;
            $data = new \ArrayObject();
        }
        $this->content = json_encode($data);
    }
}