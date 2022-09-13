<?php

use common\components\grid\DateTimeColumn;
use src\helpers\text\MaskStringHelper;
use yii\grid\ActionColumn;
use common\components\grid\BooleanColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var src\model\clientAccount\entity\ClientAccountSearch $searchModel */
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

    <?php Pjax::begin(['id' => $pjaxListId, 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ca_id',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'ca_project_id',
                'relation' => 'project',
            ],
            'ca_uuid',
            'ca_hid',
            [
                'attribute' => 'ca_username',
                'value' => static function ($model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['ca_username']);
                    return $data['ca_username'];
                }
            ],
            [
                'attribute' => 'ca_first_name',
                'value' => static function ($model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['ca_first_name']);
                    return $data['ca_first_name'];
                }
            ],
            [
                'attribute' => 'ca_middle_name',
                'value' => static function ($model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['ca_middle_name']);
                    return $data['ca_middle_name'];
                }
            ],
            [
                'attribute' => 'ca_first_name',
                'value' => static function ($model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['ca_last_name']);
                    return $data['ca_last_name'];
                }
            ],
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
