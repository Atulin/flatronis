const nav = document.querySelector('.scroll-indicator');

function scrollIndicator() {
    if (window.scrollY >= 100) {
        nav.classList.add('disabled');
    } else {
        nav.classList.remove('disabled');
    }
}

window.addEventListener('scroll', scrollIndicator);
