<?php

use src\model\leadUserData\entity\LeadUserData;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserData\entity\LeadUserData */

$this->title = $model->lud_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead User Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-user-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'lud_id' => $model->lud_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'lud_id' => $model->lud_id], [
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
                'lud_id',
                [
                    'attribute' => 'lud_type_id',
                    'value' => static function (LeadUserData $model) {
                        return $model->getTypeName();
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'lud_lead_id',
                    'value' => static function (LeadUserData $model) {
                        return Yii::$app->formatter->asLead($model->ludLead, 'fa-cubes');
                    },
                    'format' => 'raw',
                ],
                'lud_user_id:userName',
                'lud_created_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
