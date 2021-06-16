<?php

use common\components\grid\DateTimeColumn;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\leadUserConversion\entity\LeadUserConversionSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lead User Conversions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-user-conversion-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead User Conversion', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-user-conversion']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            [
                'attribute' => 'luc_lead_id',
                'value' => static function (LeadUserConversion $model) {
                    return Yii::$app->formatter->asLead($model->lucLead, 'fa-cubes');
                },
                'format' => 'raw',
            ],
            'luc_user_id:userName',
            'luc_description',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'luc_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
