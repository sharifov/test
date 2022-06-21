<?php

namespace common\components\email\dto;

use yii\base\BaseObject;

class EmailDto extends BaseObject
{
    public string $to = '';
    public string $from = '';
    public string $title = '';
    public string $body = '';
}
