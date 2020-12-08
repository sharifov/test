<?php

use frontend\assets\FormBuilderAsset;
use frontend\helpers\JsonHelper;
use yii\bootstrap4\Html;
use common\models\Project;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var sales\model\clientChatForm\entity\ClientChatForm $model */

$this->title = 'Update Client Chat Form: ' . $model->ccf_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccf_id, 'url' => ['view', 'id' => $model->ccf_id]];
$this->params['breadcrumbs'][] = 'Update';


FormBuilderAsset::register($this);
?>
<div class="client-chat-form-builder">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <?php echo $this->render('_form_builder', [
        'model' => $model,
    ]) ?>

</div>


