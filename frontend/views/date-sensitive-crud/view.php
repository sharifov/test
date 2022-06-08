<?php

use src\services\system\DbViewCryptService;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DateSensitive */

$this->title = $model->da_name;
$this->params['breadcrumbs'][] = ['label' => 'Date Sensitive', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="date-sensitive-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->da_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->da_id], [
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
            'da_id',
            'da_key',
            'da_name',
            'da_source',
            [
                'attribute' => 'da_created_user_id',
                'value' => static function (\common\models\DateSensitive $model) {
                    return $model->daCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->daCreatedUser->username) : $model->da_created_user_id;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'da_updated_user_id',
                'value' => static function (\common\models\DateSensitive $model) {
                    return $model->daUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->daUpdatedUser->username) : $model->da_updated_user_id;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'da_created_dt',
                'value' => static function (\common\models\DateSensitive $model) {
                    return $model->da_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->da_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'da_updated_dt',
                'value' => static function (\common\models\DateSensitive $model) {
                    return $model->da_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->da_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>


    <div class="row">
        <div class="col-md-6">
            <h6>View List:</h6>
            <?php if (count($model->dateSensitiveViews) > 0) : ?>
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <th>Nr</th>
                        <th>Name</th>
                        <th>Create time</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($model->dateSensitiveViews as $n => $table) : ?>
                        <tr>
                            <td><?php echo($n + 1) ?></td>
                            <td>
                                <b><?php echo Html::encode($table->dv_view_name) ?></b>
                            </td>
                            <td><?php echo Html::encode($table->dv_created_dt) ?></td>

                            <td><?=
                                Html::a('<i class="fa fa-trash" style="font-size: 100%"></i>', ['/date-sensitive/drop-view', 'viewName' => $table->dv_view_name], [
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
