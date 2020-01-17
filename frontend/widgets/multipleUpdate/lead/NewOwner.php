<?php

namespace frontend\widgets\multipleUpdate\lead;

/**
 * Class NewOwner
 *
 * @property int|null $id
 * @property string|null $userName
 */
class NewOwner
{
    public $id;
    public $userName;

    public function __construct($id, $userName)
    {
        $this->id = $id;
        $this->userName = $userName;
    }
}
