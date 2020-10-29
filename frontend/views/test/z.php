<?php
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\jui\Dialog;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

?>

<?php
    Dialog::begin([
    'clientOptions' => [
        'modal' => true,
        'autoOpen' => true,
        'title' => 'Confirm',
        'buttons' => [
            ['text' => 'Yes', 'click' => new JsExpression('function(){$(this).dialog("close");}')],
            ['text' => 'Cancel', 'click' => new JsExpression('function(){$(this).dialog("close");}')],
        ],
    ],
]);
echo 'Are you sure you want to change the type? Current data will be lost.';
Dialog::end();
?>

<?php
$js = <<<JS

  
JS;
$this->registerJs($js);
?>

<?php $form = ActiveForm::begin(); ?>

<?php
  echo AutoComplete::widget([
      'name' => 'country',
      'clientOptions' => [
          'source' => ['USA', 'RUS', 'test'],
      ],
  ]);
?>

<?php ActiveForm::end(); ?>