<?php

namespace frontend\widgets\frontendWidgetList\louassist;

use yii\helpers\ArrayHelper;

/**
 * LouAssistWidget
 *
 * @property array|null $params
 */
class LouAssistWidget extends \yii\bootstrap\Widget
{
    public $params;

    public function run()
    {
        if (!$identify = ArrayHelper::remove($this->params, 'identify')) {
            throw new \RuntimeException('"identify" is required in LouAssistWidget params');
        }
        if (!$scriptId = ArrayHelper::remove($this->params, 'scriptId')) {
            throw new \RuntimeException('"scriptId" is required in LouAssistWidget params');
        }

        return $this->render('view', [
            'params' => $this->params,
            'identify' => $identify,
            'scriptId' => $scriptId,
        ]);
    }
}
