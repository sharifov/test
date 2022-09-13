<?php

use frontend\helpers\JsonHelper;
use src\services\system\DbViewCryptService;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\dbDataSensitive\entity\DbDataSensitive */

$this->title = $model->dda_name;
$this->params['breadcrumbs'][] = ['label' => 'DB Data Sensitive', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="date-sensitive-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->dda_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->dda_id], [
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
            'dda_id',
            'dda_key',
            'dda_name',
            [
                'attribute' => 'da_created_user_id',
                'value' => static function (\src\model\dbDataSensitive\entity\DbDataSensitive $model) {
                    return $model->createdUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->createdUser->username) : $model->dda_created_user_id;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'da_updated_user_id',
                'value' => static function (\src\model\dbDataSensitive\entity\DbDataSensitive $model) {
                    return $model->updatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->updatedUser->username) : $model->dda_updated_user_id;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'da_created_dt',
                'value' => static function (\src\model\dbDataSensitive\entity\DbDataSensitive $model) {
                    return $model->dda_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dda_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'da_updated_dt',
                'value' => static function (\src\model\dbDataSensitive\entity\DbDataSensitive $model) {
                    return $model->dda_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dda_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>


    <div class="row">
        <div class="col-md-6 bg-white">
            <h2>Source:</h2>
            <?=\yii\helpers\VarDumper::dumpAsString(JsonHelper::decode($model->dda_source), 10, true) ?>
        </div>
        <div class="col-md-6">
            <h6>View List:</h6>
            <?php if (count($model->dbDataSensitiveViews) > 0) : ?>
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <th>Nr</th>
                        <th>Name</th>
                        <th>Create time</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($model->dbDataSensitiveViews as $n => $table) : ?>
                        <tr>
                            <td><?php echo($n + 1) ?></td>
                            <td>
                                <b><?php echo Html::encode($table->ddv_view_name) ?></b>
                            </td>
                            <td><?php echo Html::encode($table->ddv_created_dt) ?></td>

                            <td><?=
                                Html::a('<i class="fa fa-trash" style="font-size: 100%"></i>', ['/db-data-sensitive/drop-view', 'viewName' => $table->ddv_view_name], [
                                    'title' => 'Delete',
                                    'data-pjax' => 0,
                                    'data-method' => 'post',
                                    'data-confirm' => 'Are you sure you want to delete this item?'
                                ]);
                                ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
