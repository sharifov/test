<?php

use common\models\Employee;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\src\entities\hotelQuoteServiceLog\search\HotelQuoteServiceLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hotel Quote Service Logs';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="hotel-quote-service-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel Quote Service Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php echo $this->render('../hotel-quote-service-log/hotel_quote_log',
        [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'searchModel' => $searchModel,
        ]
    ) ?>

</div>
