<?php

use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $leadsDataProvider yii\data\ActiveDataProvider */
/* @var $casesDataProvider yii\data\ActiveDataProvider
 * @var $case \sales\entities\cases\Cases
 */

?>

<div>

    <?= $this->render('_client_info', ['model' => $model, 'case' => $case]) ?>

    <?php if ($model->isClient()) : ?>
            <?php Pjax::begin(['id' => 'client_leads_info', 'timeout' => 2000, 'enablePushState' => false]); ?>
                <?= $this->render('_leads_info', ['dataProvider' => $leadsDataProvider]) ?>
            <?php Pjax::end() ?>

            <?php Pjax::begin(['id' => 'client_cases_info', 'timeout' => 2000, 'enablePushState' => false]); ?>
                <?= $this->render('_cases_info', ['dataProvider' => $casesDataProvider]) ?>
            <?php Pjax::end() ?>
    <?php endif;?>

</div>
