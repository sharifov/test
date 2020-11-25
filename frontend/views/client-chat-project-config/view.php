<?php

use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig */

$this->title = $model->ccpcProject ? $model->ccpcProject->name . ' ( ' . $model->ccpc_project_id . ' )' : $model->ccpc_project_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Project Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-project-config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ccpc_project_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ccpc_project_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Delete Cache', ['delete-cache', 'projectId' => $model->ccpc_project_id], [
            'class' => 'btn btn-primary',
            'data' => [
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ccpc_project_id',
            //'ccpc_params_json',
            //'ccpc_theme_json',
            //'ccpc_registration_json',
            //'ccpc_settings_json',
            'ccpc_enabled:boolean',
            'ccpc_created_user_id',
            'ccpc_updated_user_id',
            'ccpc_created_dt:byUserDateTime',
            'ccpc_updated_dt:byUserDateTime',
        ],
    ]) ?>


    <div class="row">
        <div class="col-md-6">
            <h4><?=$model->getAttributeLabel('ccpc_params_json')?></h4>
            <pre>
            <?php

            try {
                echo JsonEditor::widget(
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'view'], //'text',
                            'mode' => 'view'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                        'value' => '{}'
                    ]
                );
            } catch (Exception $exception) {
                echo \yii\helpers\VarDumper::dumpAsString(\yii\helpers\Json::decode($model->ccpc_params_json), 10, true);
            }

            ?>
                </pre>


        </div>

        <div class="col-md-6">

            <h4><?=$model->getAttributeLabel('ccpc_theme_json')?></h4>
            <pre>
            <?php

            try {
                echo JsonEditor::widget(
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'view'], //'text',
                            'mode' => 'view'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                        'value' => $model->ccpc_theme_json
                    ]
                );
            } catch (Exception $exception) {
                echo \yii\helpers\VarDumper::dumpAsString(\yii\helpers\Json::decode($model->ccpc_theme_json), 10, true);
            }

            ?>
                </pre>


        </div>
    </div>


</div>
