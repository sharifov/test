<?php

namespace sales\model\client\notifications\listeners\productQuoteChangeCreated;

/**
 * Class Project
 *
 * @property int $id
 * @property string $key
 */
class Project
{
    public int $id;
    public string $key;

    public function __construct(int $id, string $key)
    {
        $this->id = $id;
        $this->key = $key;
    }
}
