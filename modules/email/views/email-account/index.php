<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\email\src\entity\emailAccount\EmailAccount;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\email\src\entity\emailAccount\search\EmailAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-account-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Email Account', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ea_id',
            'ea_email:email',
            [
                'attribute' => 'ea_protocol',
                'value' => static function (EmailAccount $model) {
                    return EmailAccount::PROTOCOL_LIST[$model->ea_protocol];
                },
                'filter' => EmailAccount::PROTOCOL_LIST,
            ],
            [
                'attribute' => 'ea_gmail_command',
                'value' => static function (EmailAccount $model) {
                    return EmailAccount::GMAIL_COMMAND_LIST[$model->ea_gmail_command] ?? null;
                },
                'filter' => EmailAccount::GMAIL_COMMAND_LIST,
            ],
            ['class' => BooleanColumn::class, 'attribute' => 'ea_active'],
            ['class' => UserSelect2Column::class, 'attribute' => 'ea_created_user_id', 'relation' => 'createdUser'],
            ['class' => UserSelect2Column::class, 'attribute' => 'ea_updated_user_id', 'relation' => 'updatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'ea_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'ea_updated_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
