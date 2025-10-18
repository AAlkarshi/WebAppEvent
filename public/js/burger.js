document.addEventListener('turbo:load', () => {
    const burger = document.getElementById('burger');
    const navLinks = document.getElementById('nav-links');

    if (!burger || !navLinks) return;

    // Supprime d'éventuels anciens listeners pour éviter les doublons
    burger.replaceWith(burger.cloneNode(true));
    const newBurger = document.getElementById('burger');

    newBurger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        newBurger.classList.toggle('open');
    });
});
