import { useI18n } from 'vue-i18n';
import { computed }  from 'vue';

export function useLocale() {
    const { locale, t } = useI18n();

    const isRtl = computed(() => locale.value === 'ar');

    function switchLocale(lang) {
        locale.value = lang;
        localStorage.setItem('locale', lang);
        document.documentElement.lang = lang;
        document.documentElement.dir  = lang === 'ar' ? 'rtl' : 'ltr';
    }

    return { locale, t, isRtl, switchLocale };
}
