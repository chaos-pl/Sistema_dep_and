/* Shared, accent-aware Granim backgrounds. Never selects Chart.js canvases. */
(() => {
    if (typeof Granim === 'undefined') return;
    // Capture before other view-specific wrappers are installed.
    const LocalGranim = Granim;
    const instances = new Map();
    const preference = matchMedia('(prefers-reduced-motion: reduce)');
    const reduced = () => preference.matches || document.body.classList.contains('reduced-motion');
    let paletteKey = '';

    function palette() {
        const css = getComputedStyle(document.body);
        const primary = css.getPropertyValue('--app-primary').trim();
        const dark = css.getPropertyValue('--app-primary-dark').trim();
        // Current appearance palettes are six-digit hex; validate before passing to Granim.
        if (!/^#[\da-f]{6}$/i.test(primary) || !/^#[\da-f]{6}$/i.test(dark)) return null;
        const deep = '#' + dark.slice(1).match(/../g)
            .map(channel => Math.round(parseInt(channel, 16) * .65).toString(16).padStart(2, '0')).join('');
        const start = css.getPropertyValue('--gradient-start').trim() || primary;
        const vivid = css.getPropertyValue('--gradient-end').trim() || primary;
        if (![start, vivid].every(color => /^#[\da-f]{6}$/i.test(color))) return null;
        return {primary: start, dark, deep, vivid, key: primary + dark + start + vivid};
    }
    function refresh() {
        const notice = document.querySelector('[data-gradient-notice]');
        if (notice) {
            const message = preference.matches
                ? 'Los fondos están estáticos porque tu dispositivo solicita reducir el movimiento.'
                : document.body.classList.contains('reduced-motion')
                    ? 'Los fondos están estáticos porque tienes activada la opción «Reducir animaciones y transiciones» en Mi perfil.'
                    : '';
            notice.hidden = !message;
            notice.querySelector('[data-gradient-message]').textContent = message;
        }
        instances.forEach(({instance, visible}, canvas) => {
            canvas.dataset.gradientState = reduced() ? 'reduced' : document.hidden || !visible ? 'paused' : 'running';
            if (reduced() || document.hidden || !visible) instance.pause();
            else instance.play();
        });
    }
    function create(canvas, colors) {
        const sidebar = canvas.classList.contains('granim-canvas-sidebar');
        const instance = new LocalGranim({
            element: '#' + canvas.id,
            direction: sidebar ? 'diagonal' : 'left-right',
            opacity: [1, 1],
            isPausedWhenNotInView: false,
            states: {
                'default-state': {
                    gradients: [
                        [colors.primary, colors.vivid],
                        [colors.dark, colors.primary],
                        [colors.vivid, colors.deep],
                        [colors.primary, colors.vivid]
                    ],
                    transitionSpeed: sidebar ? 8000 : 6000,
                    loop: true
                }
            }
        });
        // Draw a complete static frame even when reduced motion pauses immediately.
        instance.refreshColorsAndPos(0);
        return instance;
    }
    function updatePalette() {
        const colors = palette();
        if (colors && colors.key !== paletteKey) {
            paletteKey = colors.key;
            instances.forEach((item, canvas) => {
                item.instance.destroy();
                item.instance = create(canvas, colors);
            });
        }
        refresh();
    }
    function initialize() {
        const colors = palette();
        if (!colors) return;
        paletteKey = colors.key;
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                const item = instances.get(entry.target);
                if (item) item.visible = entry.isIntersecting;
            });
            refresh();
        });
        const canvases = [...document.querySelectorAll('.granim-canvas'), ...document.querySelectorAll('.granim-canvas-sidebar')];
        canvases.forEach((canvas, index) => {
            if (instances.has(canvas)) return;
            if (!canvas.id) canvas.id = 'prometeo-gradient-' + index;
            instances.set(canvas, {instance: create(canvas, colors), visible: true});
            observer.observe(canvas);
        });
        refresh();
        new MutationObserver(updatePalette).observe(document.body, {
            attributes: true, attributeFilter: ['class', 'data-accent', 'style']
        });
        preference.addEventListener('change', refresh);
        document.addEventListener('visibilitychange', refresh);
        document.addEventListener('shown.bs.offcanvas', () => {
            window.dispatchEvent(new Event('resize'));
            refresh();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, {once: true});
    else initialize();
})();
