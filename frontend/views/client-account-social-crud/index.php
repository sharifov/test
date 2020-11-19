<?php

use common\components\grid\DateTimeColumn;
use sales\model\clientAccountSocial\entity\ClientAccountSocial;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\clientAccountSocial\entity\ClientAccountSocialSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Account Socials';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-account-social-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Account Social', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-account-social']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'cas_ca_id',
            [
                'attribute' => 'cas_type_id',
                'value' => static function (ClientAccountSocial $model) {
                    if ($model->cas_type_id === ClientAccountSocial::TYPE_GOOGLE) {
                        return '<i class="fa fa-google"></i>';
                    }
                    if ($model->cas_type_id === ClientAccountSocial::TYPE_FACEBOOK) {
                        return '<i class="fa fa-facebook"></i>';
                    }
                    return '---';
                },
                'format' => 'raw',
                'filter' => ClientAccountSocial::TYPE_LIST,
            ],
            'cas_identity',
            ['class' => DateTimeColumn::class, 'attribute' => 'cas_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
