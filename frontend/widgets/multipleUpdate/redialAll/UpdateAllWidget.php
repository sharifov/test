<?php

namespace frontend\widgets\multipleUpdate\redialAll;

use yii\base\Widget;

/**
 * Class UpdateAllWidget
 *
 * @property $modalId
 * @property $showUrl
 */
class UpdateAllWidget extends Widget
{
    public $modalId;
    public $showUrl;

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('_update_all', [
            'modalId' => $this->modalId,
            'showUrl' => $this->showUrl
        ]);
    }
}
