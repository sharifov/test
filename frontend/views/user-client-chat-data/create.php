<?php

use yii\bootstrap4\Html;

/* @var yii\web\View $this */
/* @var sales\model\userClientChatData\entity\UserClientChatData $model */
/* @var string $error */

$this->title = 'Create User Client Chat Data';
$this->params['breadcrumbs'][] = ['label' => 'User Client Chat Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-client-chat-data-create">
    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>

            <?php if ($error) : ?>
                <div class="alert alert-error" role="alert">
                    <?php echo $error ?>
                </div>
            <?php endif ?>

            <?= $this->render('_create_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
