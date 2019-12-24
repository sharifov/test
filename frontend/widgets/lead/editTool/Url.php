<?php

namespace frontend\widgets\lead\editTool;

/**
 * Class Url
 *
 * @property string $url
 * @property array $data
 */
class Url
{
    public $url;
    private $data;

    /**
     * @param string $url
     * @param array $data
     */
    public function __construct(string $url, array $data = [])
    {
        $this->url = $url;
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
