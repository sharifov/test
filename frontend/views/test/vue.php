<?php
use yii\helpers\Html;
\frontend\assets\VueAsset::register($this); // register VueAsset

$this->registerJsFile('/js/vue/call-widget.js', [
    'position' => \yii\web\View::POS_END,
    'depends' => [
        \frontend\assets\VueAsset::class
    ]
]);

?>

<style>
    /* CSS file */
    .checkbox-wrapper {
        border: 1px solid;
        display: flex;
    }
    .checkbox {
        width: 50px;
        height: 50px;
        background: red;
    }
    .checkbox.checked {
        background: green;
    }
    </style>



<script type="text/x-template" id="call-widget-template">
    <div class="checkbox-wrapper" >
        <div :class="{ checkbox: true, checked: checked }"></div>
        <div class="title">{{ title }}</div>
    </div>
</script>



<div id="app"> <!-- the root Vue element -->
    <call-widget></call-widget> <!-- your component -->
    <p>{{ message }}</p>
    <button v-on:click="reverseMessage">Reverse Message</button>
</div>


<script>
    //Vue.config('debug', true);

    // this is the JS file, eg app.js
    // Vue.component('call-widget', {
    //     template: '#call-widget-template',
    //     data() {
    //         return { checked: false, title: 'Check me' }
    //     },
    //     methods: {
    //         check() { this.checked = !this.checked; }
    //     }
    // });

    // Vue.component('my-test2', {
    //     template: '#test2',
    //     data() {
    //         return { checked: false, title: 'Check me' }
    //     },
    //     methods: {
    //         check() { this.checked = !this.checked; }
    //     }
    // });

    // new Vue({el:'#app'})



</script>