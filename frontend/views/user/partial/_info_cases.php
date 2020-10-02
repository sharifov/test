<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<?php Pjax::begin() ?>
<?php /*echo $this->render('_info_leads_search', ['model' => $casesSearchModel]); */ ?>
<h5>Solved Cases</h5>
<div class="well">
    <?= GridView::widget([
        'dataProvider' => $casesInfoDataProvider,
        'filterModel' => $casesSearchModel,
        'emptyTextOptions' => [
            'class' => 'text-center'
        ],
    ]) ?>
</div>
<?php Pjax::end() ?>
