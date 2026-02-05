/*
Template Name: Fabkin - Admin & Dashboard Template
Author: Pixeleyez
Website: https://pixeleyez.com/
Contact: pixeleyez@gmail.com
File: Scroll To Top Js File
*/

const scrollToTop = document.getElementById("progress-scroll");
if (scrollToTop) {
    const scroller = document.getElementById("progress-scroll");

    if (scroller) {
        scroller.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const progressWrap = document.querySelector('.progress-wrap');
        const progressCircle = document.querySelector('.progress');
        if (!progressWrap || !progressCircle) return;

        const pathLength = 282.6; // Circumference of the circle (2 * π * radius) = 2 * π * 45
        progressCircle.style.strokeDasharray = `${pathLength} ${pathLength}`;
        progressCircle.style.strokeDashoffset = `${pathLength}`;

        let maxScrollHeight = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        let rafScheduled = false;

        const updateProgress = () => {
            rafScheduled = false;
            const scroll = window.scrollY || document.documentElement.scrollTop;
            const progress = pathLength - (scroll * pathLength / maxScrollHeight);
            progressCircle.style.strokeDashoffset = String(progress);
            progressWrap.classList.toggle('active-progress', scroll > 200);
        };

        const onScroll = () => {
            if (rafScheduled) return;
            rafScheduled = true;
            window.requestAnimationFrame(updateProgress);
        };

        const onResize = () => {
            maxScrollHeight = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
            onScroll();
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onResize, { passive: true });

        // Run once at start.
        onResize();
    });
}