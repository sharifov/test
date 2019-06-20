<?php

namespace sales\forms;

use yii\base\Model;
use yii\helpers\Html;

class CompositeFormHelper
{

    /**
     * @param array $post ex.: Yii::$app->request->post(),
     * @param string $compositeFormName ex.: formName
     * @param array $internalMultiForms ex.: ['attribute1' => 'CreatForm', 'attribute2' => 'CreateFrom2'] - only for multiInput fields
     * @return array ['post', 'keys'] ex.: ['post' => [], 'keys' => []]  post: FormatPostData, keys: Relations keys
     */
    public static function prepareDataForMultiInput(array $post, string $compositeFormName, array $internalMultiForms): array
    {
        $keys = [];
        foreach ($internalMultiForms as $attribute => $formName) {
            $post[$formName] = [];
            if (isset($post[$compositeFormName][$attribute])) {
                if (is_array($post[$compositeFormName][$attribute])) {
                    $post[$formName] = $post[$compositeFormName][$attribute];
                    [$post[$formName], $keys[$attribute]] = self::sortElements($post[$formName]);
                }
                unset($post[$compositeFormName][$attribute]);
            }
        }
        return ['post' => $post, 'keys' => $keys];
    }

    /**
     * @param array $form
     * @return array
     */
    private static function sortElements(array $form): array
    {
        $sort = [];
        $relationKeys = [];
        foreach ($form as $key => $item) {
            $sort[] = $item;
            $relationKeys[] = $key;
        }
        return [$sort, $relationKeys];
    }

    public static function ajaxValidate($model, array $keys = null, $attributes = null): array
    {
        $result = [];
        if ($attributes instanceof Model) {
            // validating multiple models
            $models = func_get_args();
            $attributes = null;
        } else {
            $models = [$model];
        }
        /* @var $model Model */
        foreach ($models as $model) {
            $model->validate($attributes);
            foreach ($model->getErrors() as $attribute => $errors) {
                if ($internalForm = strstr($attribute,'.', true)) {
                    $tmpInternalFormAttribute = strstr($attribute,'.');
                    $internalFormAttribute = substr($tmpInternalFormAttribute, 1, strlen($tmpInternalFormAttribute));
                    try {
                        $result[Html::getInputId($model->{$internalForm}, $internalFormAttribute)] = $errors;
                    } catch (\Throwable $e) {
                        $result[Html::getInputId($model, $attribute)] = $errors;
                    }
                } else {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
            }
        }
        if ($keys === null) {
            return $result;
        }
        return self::returnLastKeys($result, $keys);
    }

    /**
     * @param array $result
     * @param array $keys
     * @return array
     */
    private static function returnLastKeys(array $result, array $keys): array
    {
        $errors = [];
        foreach ($result as $key => $error) {
            $found = false;
            foreach ($keys as $attributeName => $attributeKeys) {
                foreach ($attributeKeys as $newKey => $lastKey) {
                    $forReplaceKey = str_replace($attributeName . '-' . $newKey . '-', $attributeName . '-' . $lastKey . '-', $key);
                    if ($forReplaceKey !== $key) {
                        $errors[$forReplaceKey] = $error;
                        $found = true;
                        break 2;
                    }
                }
            }
            if (!$found) {
                $errors[$key] = $error;
            }
        }
        return $errors;
    }

}
