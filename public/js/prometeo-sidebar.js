/* Navigation remains usable without Anime.js or localStorage. */
(() => {
    const preference = matchMedia('(prefers-reduced-motion: reduce)');
    const reduced = () => preference.matches || document.body.classList.contains('reduced-motion');
    function initialize() {
        document.querySelectorAll('.nav-section').forEach((section, index) => {
            const header = section.querySelector('.nav-section-header');
            const body = section.querySelector('.nav-section-body');
            if (!header || !body) return;
            const key = 'prometeo-section-' + section.dataset.sectionName;
            let collapsed = false;
            let animation;
            try { collapsed = localStorage.getItem(key) === 'collapsed'; } catch (_) { /* Storage can be disabled. */ }
            if (body.querySelector('.nav-link.active')) collapsed = false;
            body.id ||= 'prometeo-nav-section-' + index;
            header.setAttribute('role', 'button');
            header.tabIndex = 0;
            header.setAttribute('aria-controls', body.id);

            function settle() {
                animation?.cancel();
                animation = null;
                body.hidden = collapsed;
                body.style.removeProperty('height');
            }
            function render(animate = false) {
                const height = body.getBoundingClientRect().height;
                animation?.cancel();
                animation = null;
                section.classList.toggle('is-collapsed', collapsed);
                header.setAttribute('aria-expanded', String(!collapsed));
                body.inert = collapsed;
                body.hidden = false;
                if (!animate || reduced() || !body.animate) return settle();
                const current = body.animate([
                    {height: height + 'px'},
                    {height: (collapsed ? 0 : body.scrollHeight) + 'px'}
                ], {duration: 240, easing: 'ease-out', fill: 'both'});
                animation = current;
                current.onfinish = () => { if (animation === current) settle(); };
            }
            function toggle() {
                collapsed = !collapsed;
                try { localStorage.setItem(key, collapsed ? 'collapsed' : 'expanded'); } catch (_) { /* Navigation still works. */ }
                render(true);
            }
            header.addEventListener('click', toggle);
            header.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggle();
                }
            });
            preference.addEventListener('change', () => { if (reduced()) settle(); });
            new MutationObserver(() => { if (reduced()) settle(); }).observe(document.body, {
                attributes: true, attributeFilter: ['class']
            });
            render();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, {once: true});
    else initialize();
})();
