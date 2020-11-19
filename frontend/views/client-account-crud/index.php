<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use common\components\grid\BooleanColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\clientAccount\entity\ClientAccountSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Accounts';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-client-account';
?>
<div class="client-account-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Account', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?php Pjax::begin(['id' => $pjaxListId]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ca_id',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'ca_project_id',
                'relation' => 'project',
            ],
            'ca_uuid',
            'ca_hid',
            'ca_username',
            'ca_first_name',
            'ca_middle_name',
            'ca_last_name',
            //'ca_nationality_country_code',
            //'ca_dob',
            //'ca_gender',
            //'ca_phone',
            //'ca_subscription',
            //'ca_language_id',
            //'ca_currency_code',
            //'ca_timezone',
            //'ca_created_ip',
            //'ca_origin_created_dt',
            //'ca_origin_updated_dt',
            //'ca_updated_dt',
            ['class' => BooleanColumn::class, 'attribute' => 'ca_enabled'],
            ['class' => DateTimeColumn::class, 'attribute' => 'ca_created_dt'],
            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
