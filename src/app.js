window.Vue = require('vue');
window.axios = require('axios');

// Components
import Form from './components/form';




Vue.component('sms-form', Form);

const app = new Vue({

      el: "#app",

})