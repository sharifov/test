<?php

use common\components\grid\DateTimeColumn;
use src\helpers\phone\MaskPhoneHelper;
use src\model\contactPhoneList\entity\ContactPhoneList;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\contactPhoneList\entity\ContactPhoneListCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contact Phone Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-phone-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?= Html::a('Create Contact Phone List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-contact-phone-list', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'cpl_id',
            [
                'attribute' => 'cpl_phone_number',
                'value' => static function (ContactPhoneList $model) {
                    return MaskPhoneHelper::masking($model->cpl_phone_number);
                }
            ],
            'cpl_uid',
            'cpl_title',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cpl_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
