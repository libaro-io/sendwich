import type { Directive } from 'vue';

/**
 * Scroll-reveal directive — adds `.reveal` on mount and `.is-in` once the
 * element enters the viewport. Pass a number to stagger the transition
 * delay in milliseconds: `v-reveal="index * 110"`.
 *
 * The matching `.reveal` / `.is-in` styles live in the design-system block
 * of resources/css/app.css.
 */
export const vReveal: Directive<HTMLElement, number | undefined> = {
    mounted(el, binding) {
        el.classList.add('reveal');
        if (binding.value) el.style.transitionDelay = `${binding.value}ms`;
        if (!('IntersectionObserver' in window)) { el.classList.add('is-in'); return; }
        const io = new IntersectionObserver(
            (entries) => entries.forEach((e) => {
                if (e.isIntersecting) { el.classList.add('is-in'); io.unobserve(el); }
            }),
            { threshold: 0.14 },
        );
        io.observe(el);
    },
};
