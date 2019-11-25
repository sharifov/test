<?php

namespace frontend\widgets\multipleUpdate\redialAll;

use yii\base\Widget;

/**
 * Class UpdateAllShowWidget
 *
 * @property string $validationUrl
 * @property string $action
 * @property string $modalId
 * @property string $script
 */
class UpdateAllShowWidget extends Widget
{
    public $validationUrl;
    public $action;
    public $modalId;
    public $script;

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('_show', [
            'updateForm' => new UpdateAllForm(),
            'validationUrl' => $this->validationUrl,
            'action' => $this->action,
            'modalId' => $this->modalId,
            'script' => $this->script,
        ]);
    }
}
