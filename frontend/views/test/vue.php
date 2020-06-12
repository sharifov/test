<?php
use yii\helpers\Html;
\frontend\assets\VueAsset::register($this); // register VueAsset

$this->registerJsFile('/js/vue/call-widget.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \frontend\assets\VueAsset::class
    ]
]);

?>

<div id="app2" class="vue">
    <p>{{ message }}</p>
    <button v-on:click="reverseMessage">Reverse Message</button>
</div>

<div id="app">...</div>
<script type="text/x-template" id="checkbox-template">
    <div class="checkbox-wrapper" @click="check">
        <div :class="{ checkbox: true, checked: checked }"></div>
        <div class="title">{{ title }}</div>
    </div>
</script>

<script>
    var app = new Vue({
        el: '#app2',
        data: {
            message: 'Hello Vue.js!'
        },
        methods: {
            reverseMessage: function () {
                this.message = this.message.split('').reverse().join('')
            }
        }
    })
</script>