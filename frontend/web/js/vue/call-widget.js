// var cwComponent = Vue.component('call-widget', {
//     template: '#call-widget-template',
//     data() {
//         return { checked: false, title: 'Check me' }
//     },
//     methods: {
//         check() { this.checked = !this.checked; },
//         alert: function () {
//             alert(123);
//         }
//     }
// });


var ComponentA = {
    template: '#call-widget-template',
    data() {
        return { checked: false, title: 'Check me' }
    },
    methods: {
        //check() { this.checked = !this.checked; },
        alertic: function () {
            alert(123);
        }
    }
}


var app = new Vue({
    el: '#app',
    components: {
        'call-widget': ComponentA
    },
    data: {
        message: 'Hello Vue.js!'
    },
    methods: {
        reverseMessage: function () {
            this.message = this.message.split('').reverse().join('')
        }
    }
});