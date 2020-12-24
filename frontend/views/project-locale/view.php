<?php

use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectLocale\ProjectLocale */

$this->title = $model->plProject ? $model->plProject->name . ' (' . $model->pl_project_id . ')' : '';
if ($model->pl_language_id) {
    $this->title .= ' - ' . $model->pl_language_id;
}

if ($model->pl_market_country) {
    $this->title .= ' (' . $model->pl_market_country . ')';
}

$this->params['breadcrumbs'][] = ['label' => 'Project Locales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="project-locale-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->pl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            '<i class="fa fa-remove"></i> Delete',
            ['delete', 'id' => $model->pl_id],
            [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
            ]
) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'pl_id',
                    'pl_project_id',
                    'plProject.name',
                    'pl_language_id',
                    'plLanguage.name_ascii',
                    'pl_market_country',
                    'pl_default:boolean',
                    'pl_enabled:boolean',
                    //'pl_params',
                    'pl_created_dt:byUserDateTime',
                    'pl_updated_dt:byUserDateTime',
                    'pl_created_user_id:username',
                    'pl_updated_user_id:username',
                ],
            ]) ?>
        </div>
        <div class="col-md-8">
            <b>Params:</b>

            <?php
            try {
                echo JsonEditor::widget(
                    [
                        // JSON editor options
                        'clientOptions' => [
                            'modes' => ['code', 'preview', 'view'], // all available modes
                            'mode' => 'view', // default mode
                        ],
                        'collapseAll' => ['view'], // collapse all fields in "view" mode
                        //'containerOptions' => ['class' => 'container'], // HTML options for JSON editor container tag
                        'expandAll' => ['view', 'form'], // expand all fields in "tree" and "form" modes
                        'name' => 'editor', // hidden input name
                        //'options' => ['id' => 'data'], // HTML options for hidden input
                        'value' => $model->pl_params, // JSON which should be shown in editor
                    ]
                );
            } catch (Throwable $throwable) {
                echo 'Error: ' . $throwable->getMessage();
                echo '<pre>' . \yii\helpers\VarDumper::dumpAsString(@json_decode($model->pl_params, true), 10, true) . '</pre>';
            }
            ?>


        </div>
    </div>

</div>
