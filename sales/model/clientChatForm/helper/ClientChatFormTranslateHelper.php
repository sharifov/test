<?php

namespace sales\model\clientChatForm\helper;

use sales\model\clientChatForm\entity\ClientChatForm;
use Yii;

/**
 * Class ClientChatFormTranslateHelper
 */
class ClientChatFormTranslateHelper
{
    /**
     * @param ClientChatForm $clientChatForm
     * @param string|null $languageId
     * @return array
     */
    public static function translateLabel(ClientChatForm $clientChatForm, ?string $languageId = null): array
    {
        /** @var array $dataForm */
        $dataForm = $clientChatForm->ccf_dataform_json;
        foreach ($dataForm as $keyItem => $item) {
            foreach ($item as $keyValue => $value) {
                if ($keyValue === 'label') {
                    $dataForm[$keyItem][$keyValue] = Yii::t('clientChat_form', $value, [], $languageId);
                }
                if ($keyValue === 'values') {
                    foreach ($value as $keyValues => $values) {
                        foreach ($values as $valuesItemKey => $valuesItem) {
                            if ($valuesItemKey === 'label') {
                                $dataForm[$keyItem][$keyValue][$keyValues][$valuesItemKey] =
                                    Yii::t('clientChat_form', $valuesItem, [], $languageId);
                            }
                        }
                    }
                }
            }
        }
        return $dataForm;
    }
}