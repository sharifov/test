<?php

namespace modules\order\src\services;

use sales\logger\formatter\Formatter;
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

    /**
     * @param Formatter $formatter
     * @param array $formattedAttr
     * @param string|null $oldAttr
     * @param string|null $newAttr
     */
    protected function formatByFormatter(Formatter $formatter, array &$formattedAttr, ?string $oldAttr, ?string $newAttr): void
    {
        if ($newAttr) {
            $oldAttr = json_decode($oldAttr, true);
            $newAttr = json_decode($newAttr, true);
            foreach ($newAttr as $attr => $value) {
                if (!in_array($attr, $formatter->getExceptedAttributes(), false) && $oldAttr[$attr] != $newAttr[$attr]) {
                    $formattedAttr[$formatter->getFormattedAttributeLabel($attr)][1] = $formatter->getFormattedAttributeValue($attr, $value);
                    if (isset($oldAttr[$attr])) {
                        $formattedAttr[$formatter->getFormattedAttributeLabel($attr)][0] = $formatter->getFormattedAttributeValue($attr, $oldAttr[$attr]);
                    }
                }
            }
        }
    }
}
