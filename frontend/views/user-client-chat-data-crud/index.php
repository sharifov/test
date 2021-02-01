<?php

use sales\model\userClientChatData\entity\UserClientChatData;
use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\userClientChatData\entity\UserClientChatDataSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Client Chat Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-client-chat-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Client Chat Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-client-chat-data']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uccd_employee_id',
                'relation' => 'uccdEmployee',
                'placeholder' => ''
            ],
            [
                'attribute' => 'uccd_active',
                'value' => static function (UserClientChatData $model) {
                    return Yii::$app->formatter->asBooleanByLabel($model->uccd_active);
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'uccd_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'uccd_updated_dt'],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uccd_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => ''
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uccd_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => ''
            ],
            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
