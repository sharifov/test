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
<?php
$js = <<<JS

    let cchId = 39;

    function f1(id) {
        return new Promise(function(resolve, reject){
            setTimeout(function() {
                let testData = {
                    xxx: 'xxx',
                    cId: id,
                    fnName: 'f1'
                };
                console.log(testData);
                if (testData.fnName === 'f3') {
                    reject(new Error('Error in ' + testData.fnName));
                }
                resolve(testData);
            }, 3000);
        });
    }
    
    function f2(data) {
        return new Promise(function(resolve, reject){
            setTimeout(function() {
                data.fnName = 'f2';
                console.log(data);
                if (data.fnName === 'f3') {
                    reject(new Error('Error in ' + data.fnName));
                }
                resolve(data);
            }, 3000);
        });
    }
    
    function f3(data) {
        return new Promise(function(resolve, reject){
            setTimeout(function() {
                data.fnName = 'f3';
                console.log(data);
                if (data.fnName === 'f3') {
                    reject(new Error('Error in ' + data.fnName));
                }
                resolve(data);
            }, 3000);
        });
    }
    
    function f4() {        
        setTimeout(function() {            
            console.log('f4');            
        }, 3000);        
    }
    
    f1(cchId).then(function(testData) {
        return f2(testData);
    }).then(function(testData) {
        return f3(testData);
    }).then(function() {
        return f4();
    }).then(function() {
        console.log('Done!');
    }).catch(function(errorMsg) {
        alert(errorMsg);
    });
JS;

$this->registerJs($js);

?>


<?php  $form = ActiveForm::begin(); ?>

<?php
  echo AutoComplete::widget([
      'name' => 'country',
      'clientOptions' => [
          'source' => ['USA', 'RUS', 'test'],
      ],
  ]);
    ?>

<?php ActiveForm::end();


