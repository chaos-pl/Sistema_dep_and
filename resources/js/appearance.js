import anime from 'animejs';
const Granim = window.Granim;

const animations = new Set();
const gradients = new Set();
const scheme = matchMedia('(prefers-color-scheme: dark)');
const applyScheme = () => document.body.classList.toggle('system-dark', scheme.matches);
scheme.addEventListener('change', applyScheme);
const preference = matchMedia('(prefers-reduced-motion: reduce)');
const reduced = () => preference.matches || document.body.classList.contains('reduced-motion');
window.prometeoReducedMotion = reduced;

function settle(instance) {
    instance.pause();
    if (!instance.loop) instance.seek(instance.duration);
    instance.animatables.forEach(({target}) => {
        if (target instanceof Element && target.matches('[class*="anime-"], .reveal-item, .modal-container, .soft-chip, .login-anim')) {
            target.style.opacity = '1';
            target.style.transform = 'none';
        }
    });
}
function register(instance) {
    animations.add(instance);
    if (instance.loop && !instance._visibilityObserver) {
        const target = instance.animatables[0]?.target;
        if (target instanceof Element) {
            instance._visibilityObserver = new IntersectionObserver(([entry]) => {
                if (!entry.isIntersecting || reduced() || document.hidden) instance.pause();
                else instance.play();
            });
            instance._visibilityObserver.observe(target);
        }
    }
    if (reduced()) settle(instance);
    return instance;
}
function motion(options) { return register(anime(options)); }
Object.assign(motion, anime);
motion.timeline = (options) => {
    const timeline = anime.timeline(options);
    const add = timeline.add.bind(timeline);
    timeline.add = (...args) => { add(...args); register(timeline); return timeline; };
    return register(timeline);
};
window.anime = motion;
if (Granim) window.Granim = class extends Granim {
    constructor(options) {
        const palettes = JSON.parse(document.querySelector('#appearance-palettes')?.textContent || '{}');
        const palette = palettes[document.body.dataset.accent];
        if (palette && !options.prometeoPalette) options.states = {'default-state': {gradients: [[palette.sidebar_start,palette.primary],[palette.primary_dark,palette.sidebar_start]],transitionSpeed:9000}};
        super({...options, isPausedWhenNotInView: true});
        gradients.add(this);
        if (reduced()) this.pause();
    }
};

/* ---------------------------------------------------------------------------
   Login: revelación del logo original y entrada escalonada.
   Usa la instancia de Anime.js ya registrada (motion) para heredar el control
   de reduced motion y de visibilitychange que administra este archivo.
   ------------------------------------------------------------------------ */
function loginScene() { return document.querySelector('[data-login-scene]'); }

function revealLoginScene() {
    const scene = loginScene();
    if (!scene) return;
    scene.removeAttribute('data-animate');
    scene.querySelector('.auth-logo-background')?.style.removeProperty('opacity');
    scene.querySelectorAll('.auth-logo-fill').forEach(node => node.style.fillOpacity = '1');
    scene.querySelectorAll('.auth-logo-path').forEach(path => path.style.strokeDashoffset = '0');
    scene.querySelectorAll('.login-anim').forEach(node => {
        node.style.opacity = '1';
        node.style.transform = 'none';
    });
}

function initLoginScene() {
    const scene = loginScene();
    if (!scene) return;

    const links = Array.from(scene.querySelectorAll('.auth-logo-path'));
    links.forEach(path => {
        const length = Math.ceil(path.getTotalLength());
        path.style.strokeDasharray = String(length);
        path.style.strokeDashoffset = String(length);
    });

    if (reduced()) { revealLoginScene(); return; }

    scene.dataset.animate = 'on';

    // Finish every staggered vector stroke before revealing the brand and form.
    const drawingEnds = 100 + 1800 + Math.max(0, links.length - 1) * 220;
    const contentStarts = drawingEnds + 450;

    function finishIntro() {
        document.removeEventListener('keydown', skipIntro, true);
        revealLoginScene();
    }
    function skipIntro() {
        // Preserve the key's normal action (typing, Tab navigation, etc.).
        timeline.pause();
        timeline.seek(timeline.duration);
        animations.delete(timeline); // Visibility changes must not restart the intro.
        finishIntro();
    }

    const timeline = motion.timeline({easing: 'easeOutCubic', complete: finishIntro})
        .add({targets: scene.querySelectorAll('.login__aurora'), opacity: [0, 1], duration: 900, delay: motion.stagger(140)})
        .add({targets: links, strokeDashoffset: 0, duration: 1800, delay: motion.stagger(220), easing: 'easeInOutSine'}, 100)
        .add({targets: scene.querySelectorAll('.auth-logo-fill'), fillOpacity: [0, 1], duration: 450}, drawingEnds)
        .add({targets: scene.querySelector('.auth-logo-background'), opacity: [.65, .09], duration: 1700}, contentStarts)
        .add({targets: scene.querySelector('[data-anim="brand"]'), opacity: [0, 1], translateY: [-14, 0], duration: 640}, contentStarts)
        .add({targets: scene.querySelector('[data-anim="title"]'), opacity: [0, 1], translateY: [22, 0], duration: 720}, contentStarts + 180)
        .add({targets: scene.querySelectorAll('[data-anim="purpose"], [data-anim="lead"], [data-anim="pills"], [data-anim="note"]'), opacity: [0, 1], translateY: [16, 0], duration: 620, delay: motion.stagger(90)}, contentStarts + 320)
        .add({targets: scene.querySelector('[data-anim="card"]'), opacity: [0, 1], translateY: [26, 0], scale: [.985, 1], duration: 760}, contentStarts + 200)
        .add({targets: scene.querySelectorAll('[data-anim="row"]'), opacity: [0, 1], translateY: [12, 0], duration: 520, delay: motion.stagger(60)}, contentStarts + 450);

    document.addEventListener('keydown', skipIntro, {capture: true, once: true});
}
function syncMotion() {
    if (reduced()) revealLoginScene();
    animations.forEach(instance => {
        if (reduced()) settle(instance);
        else if (!instance.completed && !document.hidden) instance.play();
    });
    gradients.forEach(instance => reduced() || document.hidden ? instance.pause() : instance.play());
}
preference.addEventListener('change', syncMotion);
document.addEventListener('visibilitychange', () => {
    document.body.classList.toggle('motion-suspended', document.hidden);
    if (document.hidden) {
        animations.forEach(instance => instance.pause());
        gradients.forEach(instance => instance.pause());
    } else syncMotion();
});
document.addEventListener('DOMContentLoaded', () => {
    applyScheme();
    const form = document.querySelector('[data-appearance-form]');
    if (form) {
        const apply = () => {
            document.body.classList.remove('theme-light','theme-dark','theme-system','density-compact','density-comfortable');
            document.body.classList.add('theme-' + form.theme.value,'density-' + form.density.value);
            document.body.dataset.accent = form.accent_color.value;
            document.body.classList.toggle('reduced-motion',form.reduced_motion.checked);
            // Legacy decorative canvases; shared Granim surfaces update through body attributes.
            document.querySelectorAll('canvas[id^="granim"]:not([data-prometeo-granim])').forEach(canvas => canvas.style.visibility = 'hidden');
            syncMotion();
            form.querySelector('[data-appearance-status]').textContent = 'Vista previa aplicada. Guarda para conservar estos cambios.';
        };
        form.addEventListener('change', apply);
        form.querySelector('[data-appearance-reset]').addEventListener('click', () => {
            form.theme.value='system';form.accent_color.value='purple';form.density.value='comfortable';form.reduced_motion.checked=false;apply();
        });
    }
    const photo = document.querySelector('[data-photo-form]');
    if (photo) {
        let url;
        const input = photo.querySelector('[type=file]');
        const preview = photo.querySelector('[data-photo-preview]');
        const position = () => {
            const image = preview.querySelector('img');
            if (image) image.style.objectPosition = photo.position_x.value + '% ' + photo.position_y.value + '%';
        };
        input.addEventListener('change', () => {
            const file=input.files[0];
            input.setCustomValidity('');
            if (!file) return;
            if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 2097152) {
                input.setCustomValidity('Elige una imagen JPEG, PNG o WebP de hasta 2 MB.');
                input.reportValidity();return;
            }
            if(url) URL.revokeObjectURL(url);
            url=URL.createObjectURL(file);
            const image=new Image();image.className='profile-photo';image.alt='Vista previa del encuadre';
            image.onload=()=>{
                if(image.naturalWidth>3000||image.naturalHeight>3000) {
                    input.setCustomValidity('La imagen debe medir como máximo 3000 × 3000 píxeles.');
                    input.reportValidity();
                }
            };
            image.src=url;preview.replaceChildren(image);position();
        });
        photo.addEventListener('input',position);
        window.addEventListener('pagehide',()=>{if(url) URL.revokeObjectURL(url)});
    }
    document.querySelector('[data-toggle-password]')?.addEventListener('click', event => {
        const input=document.querySelector('#password');
        input.type=input.type==='password'?'text':'password';
        event.currentTarget.setAttribute('aria-pressed',String(input.type==='text'));
        event.currentTarget.textContent=input.type==='text'?'Ocultar':'Mostrar';
    });
    document.querySelector('[data-login-form]')?.addEventListener('submit', event=>{
        const button=event.currentTarget.querySelector('[type=submit]');
        button.disabled=true;button.textContent='Ingresando…';
    });
    window.addEventListener('pageshow',()=>{
        const button=document.querySelector('[data-login-form] [type=submit]');
        if(button){button.disabled=false;button.textContent='Iniciar sesión';}
    });
    initLoginScene();
    syncMotion();
});
