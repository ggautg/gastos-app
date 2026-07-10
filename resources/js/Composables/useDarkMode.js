import { ref, watch } from 'vue';

const isDark = ref(localStorage.getItem('darkMode') === 'true');

function aplicarTema() {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

aplicarTema();

watch(isDark, (valor) => {
    localStorage.setItem('darkMode', valor);
    aplicarTema();
});

export function useDarkMode() {
    function toggle() {
        isDark.value = !isDark.value;
    }

    return { isDark, toggle };
}