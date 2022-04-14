<?php

use common\components\grid\DateTimeColumn;
use src\model\emailQuote\entity\EmailQuote;
use src\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\emailQuote\entity\EmailQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Email Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'eq_id',
                'options' => [
                    'width' => '100px'
                ]
            ],
            [
                'attribute' => 'eq_email_id',
                'value' => static function (EmailQuote $model): string {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->eq_email_id, ['/email/view', 'id' => $model->eq_email_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            'eq_quote_id',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'eq_created_dt',
                'format' => 'byUserDateTime',
                'options' => [
                    'width' => '200px'
                ],
            ],
            [
                'attribute' => 'eq_created_by',
                'filter' => UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'eq_created_by'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '200px'
                ],
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, EmailQuote $model, $key, $index, $column): string {
                    return Url::toRoute([$action, 'eq_id' => $model->eq_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
