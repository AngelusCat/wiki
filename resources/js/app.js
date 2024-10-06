import './bootstrap';
import {createApp} from "vue";
import wiki from "../vue/wiki.vue"

const app = createApp({
    components: {
        wiki
    }
}).mount("#app");
