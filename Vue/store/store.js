import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import root from './modules/root';
import content_forms from './modules/content_forms';

export const store = new Vuex.Store({
    modules: {
        root: root,
        content_forms: content_forms,
    }
});
