<?php

namespace frontend\widgets\multipleUpdate\redialAll;

use yii\base\Widget;

class UpdateAllWidget extends Widget
{
    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('_update_all');
    }
}
