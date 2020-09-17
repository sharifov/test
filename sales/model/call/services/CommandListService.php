<?php

namespace sales\model\call\services;

use sales\helpers\app\AppHelper;
use sales\model\call\entity\callCommand\CallCommand;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class CallCommandTypeService
 */
class CommandListService
{
    /**
     * @param CallCommand $model
     * @return array
     */
    public static function childrenSaver(CallCommand $model): array
    {
        $result = ['added' => 0, 'failed' => 0];

        if ((int) $model->ccom_type_id !== CallCommand::TYPE_COMMAND_LIST) {
            throw new \DomainException('Type of the parent model must be ' .
                CallCommand::getTypeName(CallCommand::TYPE_COMMAND_LIST));
        }

        if (is_array($model->ccom_params_json) && !empty($model->ccom_params_json)) {
            foreach ($model->ccom_params_json as $key => $item) {

                try {
                    $childrenModel = new CallCommand();
                    $childrenModel->ccom_parent_id = $model->ccom_id;
                    $childrenModel->ccom_project_id = $model->ccom_project_id;
                    $childrenModel->ccom_lang_id = $model->ccom_lang_id;
                    $childrenModel->ccom_user_id = $model->ccom_user_id;
                    $childrenModel->ccom_sort_order = $item['sort'];
                    $childrenModel->ccom_type_id = $item['typeId'];
                    $childrenModel->ccom_params_json = $item;

                    if (!$childrenModel->save()) {
                        Yii::error(VarDumper::dumpAsString($childrenModel->errors),
                        'CommandListService:childrenSaver::save');
                        $result['failed']++;
                    } else {

                        $items = (array) $model->ccom_params_json;
                        $items[$key]['additional']['model_id'] = $childrenModel->ccom_id;
                        $model->ccom_params_json = $items;

                        if ($model->save(false)) {
                            $result['added']++;
                        }
                    }
                } catch (\Throwable $throwable) {
                    AppHelper::throwableLogger($throwable,
                    'CommandListService:childrenSaver:throwable');
                    $result['failed']++;
                }
            }
        }

        return $result;
    }



    public static function refreshParentJson(CallCommand $model): void
    {
        if ($model->ccom_parent_id && $parentModel = CallCommand::findOne($model->ccom_parent_id)) {
            if ($parentModel->ccom_type_id === CallCommand::TYPE_COMMAND_LIST) {
                $resultJson = [];
                if ($childes = $parentModel->callCommands) {
                    foreach ($childes as $key => $childrenModel) {
                        $resultJson[$key] = (array) $childrenModel->ccom_params_json;
                        $resultJson[$key]['additional']['model_id'] = $childrenModel->ccom_id;
                    }
                }
                $parentModel->ccom_params_json = $resultJson;
                $parentModel->save(false);
            }
        }
    }
}