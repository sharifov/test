<?php

use common\components\grid\DateTimeColumn;
use common\models\Airline;
use sales\model\airline\service\AirlineService;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\airline\entity\AirlineSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Airlines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="airline-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Airline', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization from TravelServices', ['synchronization'], ['class' => 'btn btn-warning js-btn-sync']) ?>
    </p>
    <p>
        Synchronization from: <i><?php echo Html::encode(Yii::$app->travelServices->url . AirlineService::SERVICE_ENDPOINT); ?></i>
    </p>

    <?php Pjax::begin(['id' => 'pjax-airline']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'iata',
            'name',
            'code',
            [
                'attribute' => 'countryCode',
                'filter' => AirlineService::getCountryCodeList(),
            ],
            [
                'attribute' => 'country',
                'filter' => AirlineService::getCountryList(),
            ],
            [
                'header' => 'Additional',
                'value' => static function (Airline $model) {
                    $result = '';
                    if ($model->cl_economy) {
                        $result .= 'Economy: ' .  Html::encode($model->cl_economy) . '<br />';
                    }
                    if ($model->cl_premium_economy) {
                        $result .= 'Premium economy: ' .  Html::encode($model->cl_premium_economy) . '<br />';
                    }
                    if ($model->cl_business) {
                        $result .= 'Business: ' .  Html::encode($model->cl_business) . '<br />';
                    }
                    if ($model->cl_premium_business) {
                        $result .= 'Premium Business: ' .  Html::encode($model->cl_premium_business) . '<br />';
                    }
                    if ($model->cl_first) {
                        $result .= 'First: ' .  Html::encode($model->cl_first) . '<br />';
                    }
                    if ($model->cl_premium_first) {
                        $result .= 'Premium First: ' .  Html::encode($model->cl_premium_first) . '<br />';
                    }
                    return $result;
                },
                'format' => 'raw',
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'updated_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$js = <<<JS
    $(document).on('click', '.js-btn-sync', function() {
        if(!confirm('Are you sure you want synchronization all Airline from TravelServices?')) {
            return false;
        }
        let spin = '<i class="fa fa-cog fa-spin" style="font-size: 14px;"></i>';
        $(this).prop('disabled', true);
                        
        $('.preloader__text').html('Data synchronization request started.<br> Please, wait.');
        $('#page-loader').show(); 
        
        setTimeout(function () {
            $('.preloader__text').html('Data preparing.<br> Please, wait.');
        }, 10000);
        
        setTimeout(function () {
            $('.preloader__text').html('Data processing.<br> Please, wait.  ' + spin);
        }, 20000);
        
        $(location).attr('href', $(this).attr('href'));
        
        setTimeout(function () {
            $(this).prop('disabled', false);
            $('#page-loader').hide(); 
        }, 30000);
    });
JS;

$this->registerJs($js);
