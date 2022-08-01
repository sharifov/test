const { createApp } = Vue;
const { loadModule } = window['vue3-sfc-loader'];

const options = {
    moduleCache: {
        vue: Vue
    },
    getFile(url) {
        return fetch(url).then((resp) =>
            resp.ok ? resp.text() : Promise.reject(resp)
        );
    },
    addStyle(styleStr) {
        const style = document.createElement("style");
        style.textContent = styleStr;
        const ref = document.head.getElementsByTagName("style")[0] || null;
        document.head.insertBefore(style, ref);
    },
    log(type, ...args) {
        console.log(type, ...args);
    }
};

const mountEl = document.querySelector("#app");
const app = createApp({
    components: {
        VueMainComponent: Vue.defineAsyncComponent(() =>
            loadModule('/js/monitor/vue-components/vue-main-component.vue', options)
        )
    },
    props: {
        cfchannelname: String,
        cfuseronlinechannel: String,
        cftoken: String,
        cfconnectionurl: String,
        cfuserstatuschannel: String
    },
    template: '<vue-main-component ' +
        ':cfchannelname="cfchannelname" ' +
        ':cfuseronlinechannel="cfuseronlinechannel" ' +
        ':cftoken="cftoken" ' +
        ':cfconnectionurl="cfconnectionurl" ' +
        ':cfuserstatuschannel="cfuserstatuschannel"></vue-main-component>'
}, { ...mountEl.dataset }).mount('#app');
