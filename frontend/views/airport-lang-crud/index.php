<?php

use common\components\grid\UserSelect2Column;
use sales\model\airportLang\service\AirportLangService;
use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\airportLang\entity\AirportLangSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Airport Langs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="airport-lang-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Airport Lang', ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization from TravelServices', ['synchronization'], ['class' => 'btn btn-warning js-btn-sync']) ?>
    </p>
    <p>
        Synchronization from: <i><?php echo Html::encode(Yii::$app->travelServices->url . AirportLangService::SERVICE_ENDPOINT); ?></i>
    </p>

    <?php Pjax::begin(['id' => 'pjax-airport-lang']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ail_iata',
            'ail_lang',
            'ail_name',
            'ail_city',
            'ail_country',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ail_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Created User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ail_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Updated User'
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'ail_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'ail_updated_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$js = <<<JS
    $(document).on('click', '.js-btn-sync', function() {
        if(!confirm('Are you sure you want synchronization all airports from TravelServices?')) {
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
