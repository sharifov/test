<?php

use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\phoneNumberRedial\entity\PhoneNumberRedialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Number Redials';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-number-redial-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Number Redial', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-PhoneNumberRedial22']); ?>

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pnr_id',
            'pnr_project_id:projectName',
            'pnr_phone_pattern',
            [
                'attribute' => 'pnr_pl_id',
                'value' => static function (\src\model\phoneNumberRedial\entity\PhoneNumberRedial $model): string {
                    return Html::encode($model->phoneList->pl_phone_number);
                }
            ],
            'pnr_name',
            'pnr_enabled:booleanByLabel',
            'pnr_priority',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pnr_created_dt'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pnr_updated_dt'
            ],
            [
                'attribute' => 'pnr_updated_user_id',
                'filter' => \src\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'pnr_updated_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '150px'
                ]
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, \src\model\phoneNumberRedial\entity\PhoneNumberRedial $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'pnr_id' => $model->pnr_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
