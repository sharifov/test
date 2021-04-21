<?php

namespace modules\order\src\services;

use sales\services\EntityAttributeFormatService;

class OrderEntityAttributeFormatterService extends EntityAttributeFormatService
{
    public function formatAttr(string $modelPath, ?string $oldAttr, ?string $newAttr): ?string
    {
        $formattedAttr = [];

        try {
            $model = \Yii::createObject($modelPath);

            $formatterName = 'modules\\order\\src\\formatter\\' . (new \ReflectionClass($modelPath))->getShortName() . 'Formatter';

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
            \Yii::error($e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine(), 'Console:LoggerController:formAttr:Throwable');

            return null;
        }
    }
}
