<?php

namespace frontend\widgets\multipleUpdate\redial;

use yii\base\Widget;

/**
 * Class MultipleUpdateWidget
 *
 * @property string $gridId
 * @property string $script
 * @property string $actionUrl
 * @property string $validationUrl
 * @property string reportWrapperId
 */
class MultipleUpdateWidget extends Widget
{
    public $gridId;

    public $script;

    public $actionUrl;

    public $validationUrl;

    public $reportWrapperId;

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('_multiple_update', [
            'gridId' => $this->gridId,
            'script' => $this->script,
            'actionUrl' => $this->actionUrl,
            'validationUrl' => $this->validationUrl,
            'reportWrapperId' => $this->reportWrapperId,
        ]);
    }
}
