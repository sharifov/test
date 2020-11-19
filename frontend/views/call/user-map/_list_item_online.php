<?php

use sales\model\call\useCase\assignUsers\UserRenderer;

/* @var $model \common\models\UserConnection */
/* @var $index int */

?>

<div class="col-md-6" style="margin-bottom: 5px">
    <?= ($index + 1) ?>. <?= UserRenderer::render($model->ucUser) ?>
</div>
