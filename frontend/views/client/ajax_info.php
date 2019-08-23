<?php

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $leadsDataProvider yii\data\ActiveDataProvider */
/* @var $casesDataProvider yii\data\ActiveDataProvider */

?>

<div>

    <?= $this->render('_client_info', ['model' => $model]) ?>

    <?php if (Yii::$app->user->can('leadSection')): ?>
        <?= $this->render('_leads_info', ['dataProvider' => $leadsDataProvider]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->user->can('caseSection')): ?>
        <?= $this->render('_cases_info', ['dataProvider' => $casesDataProvider]) ?>
    <?php endif; ?>

</div>
