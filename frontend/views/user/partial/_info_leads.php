<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<?php Pjax::begin() ?>
<?= $this->render('_info_leads_search', ['model' => $leadsSearchModel]); ?>
<?= GridView::widget([
    'dataProvider' => $leadsInfoDataProvider,
    'filterModel' => $leadsSearchModel,
]) ?>
<?php Pjax::end() ?>
