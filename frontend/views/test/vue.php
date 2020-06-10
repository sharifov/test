<?php
use yii\helpers\Html;
\frontend\assets\VueAsset::register($this); // register VueAsset

?>

<div id="app" class="vue">
    <p>{{ message }}</p>
    <button v-on:click="reverseMessage">Reverse Message</button>
</div>

<script>
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