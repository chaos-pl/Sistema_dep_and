import './bootstrap';

import Alpine from 'alpinejs';
import { animate, stagger } from 'animejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.prometeo-modal-page');
    if (!page) return;

    animate('.fx-orb-1', {
        translateY: [-18, 18], translateX: [0, 14], scale: [1, 1.08],
        duration: 4200, loop: true, alternate: true, ease: 'inOutQuad'
    });

    animate('.fx-orb-2', {
        translateY: [16, -16], translateX: [0, -12], scale: [1, 1.06],
        duration: 4800, loop: true, alternate: true, ease: 'inOutQuad'
    });

    animate('.modal-container', {
        opacity: [0, 1], translateY: [40, 0], scale: [0.96, 1],
        duration: 1100, ease: 'outCubic'
    });

    animate('.modal-badge', {
        scale: [0.78, 1], rotate: [-10, 0],
        duration: 950, delay: 180, ease: 'outExpo'
    });

    const revealItems = document.querySelectorAll('.reveal-item');
    if (revealItems.length) {
        animate(revealItems, {
            opacity: [0, 1], translateY: [26, 0],
            delay: stagger(90, { start: 180 }), duration: 850, ease: 'outCubic'
        });
    }

    const chips = document.querySelectorAll('.soft-chip');
    if (chips.length) {
        animate(chips, {
            opacity: [0, 1], scale: [0.9, 1],
            delay: stagger(70, { start: 320 }), duration: 700, ease: 'outQuad'
        });
    }

    document.querySelectorAll('.btn-animated').forEach((button) => {
        button.addEventListener('mouseenter', () => {
            animate(button, { scale: [1, 1.03], duration: 220, ease: 'outQuad' });
        });
        button.addEventListener('mouseleave', () => {
            animate(button, { scale: [1.03, 1], duration: 220, ease: 'outQuad' });
        });
    });
});

import Granim from 'granim';
window.Granim = Granim;
