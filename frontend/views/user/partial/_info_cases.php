<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<?php Pjax::begin() ?>
<?= $this->render('_info_leads_search', ['model' => $casesSearchModel]); ?>
<?= GridView::widget([
    'dataProvider' => $casesInfoDataProvider,
    'filterModel' => $casesSearchModel,
]) ?>
<?php Pjax::end() ?>
