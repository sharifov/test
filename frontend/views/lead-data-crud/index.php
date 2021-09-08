<?php

use common\components\grid\DateTimeColumn;
use sales\model\leadDataKey\entity\LeadDataKey;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\leadData\entity\LeadDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-data', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ld_id',
            'ld_lead_id',
            [
                'attribute' => 'ld_field_key',
                'filter' => LeadDataKey::getListCache(),
            ],
            'ld_field_value',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ld_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
