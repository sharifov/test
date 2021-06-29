<?php

use common\components\grid\DateTimeColumn;
use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneData\entity\ContactPhoneData;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\contactPhoneData\entity\ContactPhoneDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contact Phone Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-phone-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Contact Phone Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-contact-phone-data']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'cpd_cpl_id',
            [
                'attribute' => 'cpd_key',
                'filter' => ContactPhoneDataDictionary::KEY_LIST
            ],
            'cpd_value',
            [
                'attribute' => 'phone',
                'label' => 'Phone',
                'value' => static function (ContactPhoneData $model) {
                    return $model->cpdCpl->cpl_phone_number;
                }
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cpd_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cpd_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
