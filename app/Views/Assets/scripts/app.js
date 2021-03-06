try {
    window.Vue = require('vue');

    window.Vuetify = require('vuetify');
    const Toasted = require('vue-toasted');

    window.axios = require('axios');

    window.Swal = require('sweetalert2');

    window.axios.defaults.headers.common['X-Requested-With'] = 'ILoveDeveloper';

} catch (e) {
}


const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => {
    Vue.component(key.split('/').pop().split('.')[0], files(key).default)
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.use(Toasted);
const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
});