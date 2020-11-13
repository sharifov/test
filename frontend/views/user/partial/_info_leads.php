<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<?php Pjax::begin() ?>
<?php /*echo $this->render('_info_leads_search', ['model' => $leadsSearchModel]); */ ?>
<h5>Booked Leads</h5>
<div class="well">
    <?= GridView::widget([
        'dataProvider' => $leadsInfoDataProvider,
        'filterModel' => $leadsSearchModel,
        'emptyTextOptions' => [
            'class' => 'text-center'
        ]
    ]) ?>
</div>
<?php Pjax::end() ?>
