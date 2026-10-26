import { computed, ref } from 'vue';

const STORAGE_KEY = 'theme';
const LEGACY_KEY = 'darkMode';
const DEFAULT_THEME = 'bosque';

export const THEMES = [
    {
        id: 'bosque',
        name: 'Bosque',
        hint: 'El preset de siempre',
        swatch: ['#f1f4ee', '#0e4f49', '#b5883f'],
        themeColor: '#0E4F49',
        dark: false,
    },
    {
        id: 'noche',
        name: 'Noche',
        hint: 'Oscuro, para descansar la vista',
        swatch: ['#071310', '#2e9488', '#d9b36c'],
        themeColor: '#071310',
        dark: true,
    },
    {
        id: 'mandioca',
        name: 'Mandioca',
        hint: 'Crema cálida y tierra adentro',
        swatch: ['#f7f0e1', '#8a5a2b', '#c08a2e'],
        themeColor: '#8A5A2B',
        dark: false,
    },
    {
        id: 'terere',
        name: 'Tereré',
        hint: 'Verde yerba, fresco y sereno',
        swatch: ['#edf2e5', '#35603f', '#9aa03a'],
        themeColor: '#35603F',
        dark: false,
    },
    {
        id: 'verano',
        name: 'Verano paraguayo',
        hint: 'Sol pleno, cielo y tierra colorada',
        swatch: ['#fff6e7', '#16697f', '#d99a1f'],
        themeColor: '#16697F',
        dark: false,
    },
];

function leerGuardado() {
    try {
        const guardado = localStorage.getItem(STORAGE_KEY);
        if (THEMES.some((preset) => preset.id === guardado)) {
            return guardado;
        }

        // Compatibilidad con el interruptor claro/oscuro anterior.
        if (localStorage.getItem(LEGACY_KEY) === 'true') {
            return 'noche';
        }
    } catch (error) {
        /* sin acceso a localStorage */
    }

    return DEFAULT_THEME;
}

export const theme = ref(leerGuardado());

export function aplicarTema(id = theme.value) {
    const preset = THEMES.find((item) => item.id === id) ?? THEMES[0];

    if (typeof document !== 'undefined') {
        const root = document.documentElement;
        root.dataset.theme = preset.id;
        root.classList.toggle('dark', preset.dark);

        document
            .querySelectorAll('meta[name="theme-color"]')
            .forEach((meta) => meta.setAttribute('content', preset.themeColor));
    }

    try {
        localStorage.setItem(STORAGE_KEY, preset.id);
        localStorage.setItem(LEGACY_KEY, preset.dark ? 'true' : 'false');
    } catch (error) {
        /* sin acceso a localStorage */
    }

    return preset;
}

aplicarTema(theme.value);

export function useTheme() {
    const isDark = computed(() => theme.value === 'noche');

    function setTheme(id) {
        if (!THEMES.some((preset) => preset.id === id)) {
            return;
        }

        theme.value = id;
        aplicarTema(id);
    }

    return { theme, themes: THEMES, isDark, setTheme };
}
