<?php

namespace src\services;

use src\logger\formatter\Formatter;
use yii\db\ActiveRecord;

abstract class EntityAttributeFormatService
{
    abstract public function formatAttr(string $modelPath, ?string $oldAttr, ?string $newAttr): ?string;

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
                if (!in_array($attr, $formatter->getExceptedAttributes(), false)) {
                    $formattedAttr[$formatter->getFormattedAttributeLabel($attr)][1] = $formatter->getFormattedAttributeValue($attr, $value);
                    if (isset($oldAttr[$attr])) {
                        $formattedAttr[$formatter->getFormattedAttributeLabel($attr)][0] = $formatter->getFormattedAttributeValue($attr, $oldAttr[$attr]);
                    }
                }
            }
        }
    }

    /**
     * @param ActiveRecord $model
     * @param array $formattedAttr
     * @param string|null $oldAttr
     * @param string|null $newAttr
     */
    protected function formatByModel(ActiveRecord $model, array &$formattedAttr, ?string $oldAttr, ?string $newAttr): void
    {
        if ($newAttr) {
            $oldAttr = ($oldAttr !== null) ? json_decode($oldAttr, true) : [];
            $newAttr = json_decode($newAttr, true);
            foreach ($newAttr as $attr => $value) {
                $formattedAttr[$model->getAttributeLabel($attr)][1] = $value;
                if (isset($oldAttr[$attr])) {
                    $formattedAttr[$model->getAttributeLabel($attr)][0] = $oldAttr[$attr];
                }
            }
        }
    }

    /**
     * @param array $errors
     * @return string
     */
    protected function getParsedErrors(array $errors): string
    {
        return implode('<br>', array_map(static function ($errors) {
            return implode('<br>', $errors);
        }, $errors));
    }
}
