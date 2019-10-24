<?php

namespace frontend\widgets\redial;

/**
 * Class RedialUrl
 *
 * @property string $url
 * @property string $method
 * @property array $data
 */
class RedialUrl
{
    public $url;
    public $method;
    private $data;

    /**
     * @param string $url
     * @param string $method
     * @param array $data
     */
    public function __construct(string $url, string $method, array $data = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->data = $data;
    }

    /**
     * @return false|string
     */
    public function getData()
    {
        return json_encode($this->data);
    }
}
