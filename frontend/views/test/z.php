<?php
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

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