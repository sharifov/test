<?php
/** @var \yii\web\View $this */
/** @var string $key */
/** @var array $object */
?>
<?php $types = implode(', ', $object['type']) ?>
<?= "<b>{$key}</b> ({$types}) {$object['description']}" ?>
<?php if (in_array('object', $object['type'])) : ?>
    <ul>
        <?php foreach ($object['data'] as $objKey => $objData) : ?>
            <li><?= $this->render('_scenario-description', [
                'key' => $objKey,
                'object' => $objData,
            ]) ?></li>
        <?php endforeach; ?>
    </ul>
<?php elseif (in_array('array', $object['type'])) : ?>
    <br><code><?= \yii\helpers\Json::encode($object['data']) ?></code>
<?php endif; ?>

