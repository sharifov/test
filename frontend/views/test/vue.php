<?php
use yii\helpers\Html;
\frontend\assets\VueAsset::register($this); // register VueAsset

$this->registerJsFile('/js/vue/call-widget.js', [
    'position' => \yii\web\View::POS_READY,
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



<script type="text/x-template" id="checkbox-template">
    <div class="checkbox-wrapper" @click="check">
        <div :class="{ checkbox: true, checked: checked }"></div>
        <div class="title">{{ title }}</div>
    </div>
</script>


<script type="text/x-template" id="test2">
    <div>
        <p>Этот шаблон будет скомпилирован в области видимости компонента-потомка.</p>
        <p>Доступа к данным родителя нет.</p>
    </div>
</script>


<div id="app"> <!-- the root Vue element -->
    <my-checkbox></my-checkbox> <!-- your component -->
    <p>{{ message }}</p>
    <button v-on:click="reverseMessage">Reverse Message</button>
    <my-test2></my-test2>
</div>


<script>

    // this is the JS file, eg app.js


    Vue.component('my-test2', {
        //template: '#test2',
        template: require('./js/vue/1.html'),
        data() {
            return { checked: false, title: 'Check me' }
        },
        methods: {
            check() { this.checked = !this.checked; }
        }
    });

    // new Vue({el:'#app'})


    var app = new Vue({
        el: '#app',
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