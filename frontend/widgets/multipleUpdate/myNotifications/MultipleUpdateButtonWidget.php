<?php

namespace frontend\widgets\multipleUpdate\myNotifications;

use yii\base\Widget;

/**
 * Class MultipleUpdateButtonWidget
 *
 * @property string $gridId
 * @property string $pjaxId
 */
class MultipleUpdateButtonWidget extends Widget
{
    public $gridId;
    public $pjaxId;

    public function init(): void
    {
        parent::init();
        if ($this->gridId === null) {
            throw new \InvalidArgumentException('gridId must be set');
        }
    }

    public function run(): string
    {
        return $this->render('button', [
            'gridId' => $this->gridId,
            'pjaxId' => $this->pjaxId,
        ]);
    }
}
