import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";

import axios from "axios";
// import VueAxios from "vue-axios";
import _lodash from "lodash";
import marked from "marked";

import moment from "moment";

import PrimeVue from "primevue/config";
import "primevue/resources/themes/saga-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
import "primeflex/primeflex.css";
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import { FontAwesomeIcon } from "./plugins/font-awesome";

import setupInterceptors from "./services/setupInterceptors";
import tempoUtils from "./tempoUtils";

setupInterceptors(store);

// window.tempoApiPath = "http://localhost/dreamhigh/api/";
window.tempoApiPath = "https://tempodev.excitemedia.com.au/api/";
window.tempoApiUsername = "excitemedia";
window.tempoApiPassword = "af9F4244sfljv4724FDkjsdfj";

const app = createApp(App);

app.config.globalProperties.$_axios = axios;
app.config.globalProperties.$_lodash = _lodash;
app.config.globalProperties.$_moment = moment;

app.config.globalProperties.$_tempoUser = {
  username: "Scott",
  faveBand: "Pearl Jam",
};

// app.use(VueAxios);
app.use(PrimeVue);
app.use(router);
app.use(store);
app.use(tempoUtils);

app.component("font-awesome-icon", FontAwesomeIcon);
app.mount("#app");
