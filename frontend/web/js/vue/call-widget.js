Vue.component('my-checkbox', {
    template: '#checkbox-template',
    data() {
        return { checked: false, title: 'Check me' }
    },
    methods: {
        check() { this.checked = !this.checked; }
    }
});