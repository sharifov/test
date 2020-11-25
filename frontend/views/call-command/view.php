<?php

use sales\model\call\entity\callCommand\CallCommand;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallCommand */

$name = $model->ccom_name ? ', Name: ' . $model->ccom_name : '';
$typeName = CallCommand::getTypeName($model->ccom_type_id);

$this->title = 'Update Call Command. Type: ' . $typeName . ', Id: ' . $model->ccom_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-command-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ccom_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ccom_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ccom_id',
            'ccom_parent_id',
            [
                'attribute' => 'ccom_project_id',
                'value' => static function (CallCommand $model) {
                    return Yii::$app->formatter->asProjectName($model->ccomProject);
                },
                'format' => 'raw',
            ],
            'ccom_lang_id',
            'ccom_name',
            [
                'attribute' => 'ccom_type_id',
                'value' => static function (CallCommand $model) {
                    return $model::getTypeName($model->ccom_type_id) ?: Yii::$app->formatter->nullDisplay;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'ccom_params_json',
                'value' => static function (CallCommand $model) {
                    return '<pre>' . VarDumper::dumpAsString($model->ccom_params_json, 10, true) . '</pre>';
                },
                'format' => 'raw',
            ],
            'ccom_sort_order',
            'ccom_user_id:userName',
            'ccom_created_user_id:userName',
            'ccom_updated_user_id:userName',
            'ccom_created_dt:byUserDateTime',
            'ccom_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
