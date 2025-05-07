import { createI18n } from 'vue-i18n'
import de from './locales/de.json'
import fr from './locales/fr.json'

const i18n = createI18n({
    legacy: false,
    locale: 'de',
    fallbackLocale: 'de',
    messages: {
        de,
        fr
    },
    silentTranslationWarn: true,
    silentFallbackWarn: true,
    missingWarn: false,
    fallbackWarn: false
})

export default i18n 