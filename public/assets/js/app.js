/*
Template Name: Fabkin - Admin & Dashboard Template
Author: Pixeleyez
Website: https://pixeleyez.com/
Contact: pixeleyez@gmail.com
File: app js
*/

// Initialize Bootstrap tooltips and popovers
function initializeBootstrapComponents(selector, Component) {
    const triggerList = document.querySelectorAll(selector);
    return [...triggerList].map((triggerEl) => new Component(triggerEl));
}

// Initialize tooltips
const tooltips = initializeBootstrapComponents(
    '[data-bs-toggle="tooltip"]',
    bootstrap.Tooltip
);

// Initialize popovers
const popovers = initializeBootstrapComponents(
    '[data-bs-toggle="popover"]',
    bootstrap.Popover
);

// Function to handle both sticky menu and button loading
function initializeAppFeatures() {
    const stickyMenu = document.getElementById("appHeader"); // Ensure this ID matches your HTML
    let stickyOffset = stickyMenu ? stickyMenu.offsetTop : 0;

    // Toggle sticky class on scroll (throttled to animation frame).
    let rafScheduled = false;
    let lastShouldStick = null;

    function updateStickyMenu() {
        rafScheduled = false;
        if (!stickyMenu) return;

        const shouldStick = window.scrollY > stickyOffset;
        if (shouldStick === lastShouldStick) return;
        lastShouldStick = shouldStick;

        stickyMenu.classList.toggle("sticky-scroll", shouldStick);
    }

    function onScroll() {
        if (rafScheduled) return;
        rafScheduled = true;
        window.requestAnimationFrame(updateStickyMenu);
    }

    if (stickyMenu) {
        // Attach a passive scroll listener for smoother scrolling.
        window.addEventListener("scroll", onScroll, { passive: true });
        window.addEventListener(
            "resize",
            () => {
                stickyOffset = stickyMenu.offsetTop;
                onScroll();
            },
            { passive: true }
        );

        // Run once at start.
        onScroll();
    }

    // Attach click event listeners to all loader buttons
    const loaderButtons = document.querySelectorAll(".btn-loader");
    if (!loaderButtons.length) return;

    loaderButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const indicatorLabel = this.querySelector(".indicator-label");
            const originalText = indicatorLabel.textContent;
            const loadingText = this.getAttribute("data-loading-text");

            // Show loading indicator and disable button
            this.classList.add("loading");
            indicatorLabel.textContent = loadingText;
            this.disabled = true;

            // Simulate an asynchronous operation (e.g., form submission)
            setTimeout(() => {
                // Hide loading indicator and reset button
                this.classList.remove("loading");
                indicatorLabel.textContent = originalText;
                this.disabled = false;
            }, 1500); // Simulated delay of 1.5 seconds
        });
    });
}

// Call the function to initialize features
document.addEventListener("DOMContentLoaded", initializeAppFeatures);
