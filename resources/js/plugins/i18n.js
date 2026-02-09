

import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n);

export const i18n = new VueI18n({
    locale: document.querySelector('html').getAttribute('lang'),
    fallbackLocale: 'en',
    silentFallbackWarn: true,
    silentTranslationWarn: true,
    messages: window.vuei18nLocales
});
