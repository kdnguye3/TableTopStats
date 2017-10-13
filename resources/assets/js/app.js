
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.moment = require('moment');

import Buefy from 'buefy'
import 'buefy/lib/buefy.css'

Vue.use(Buefy)


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('leaderboard', require('./components/Leaderboard.vue'));
Vue.component('playertable', require('./components/Playertable.vue'));
Vue.component('gameindex', require('./components/GameIndex.vue'));
Vue.component('gametable', require('./components/GameTable.vue'));
Vue.component('randomchart', require('./components/randomchart.vue'));






const app = new Vue({
    el: '#app',
});

