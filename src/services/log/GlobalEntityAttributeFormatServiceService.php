<?php

namespace src\services\log;

use src\helpers\app\AppHelper;
use src\services\EntityAttributeFormatService;
use yii\helpers\ArrayHelper;

class GlobalEntityAttributeFormatServiceService extends EntityAttributeFormatService
{
    /**
     * @param string $modelPath
     * @param string $oldAttr
     * @param string $newAttr
     * @return string|null
     */
    public function formatAttr(string $modelPath, ?string $oldAttr, ?string $newAttr): ?string
    {
        $formattedAttr = [];

        try {
            $model = \Yii::createObject($modelPath);

            $formatterName = 'src\\logger\\formatter\\' . (new \ReflectionClass($modelPath))->getShortName() . 'Formatter';

            if (class_exists($formatterName)) {
                $formatter = \Yii::createObject($formatterName);
                $this->formatByFormatter($formatter, $formattedAttr, $oldAttr, $newAttr);
            } else {
                $this->formatByModel($model, $formattedAttr, $oldAttr, $newAttr);
            }

            if (empty($formattedAttr)) {
                return null;
            }

            return json_encode($formattedAttr);
        } catch (\Throwable $e) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($e), [
                'modelPath' => $modelPath,
                'oldAttr' => $oldAttr,
                'newAttr' => $newAttr,
            ]);
            \Yii::error($message, 'Console:LoggerController:formAttr:Throwable');

            return null;
        }
    }
}
