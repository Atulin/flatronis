const ind = document.querySelector('.scroll-indicator');

function scrollIndicator() {
    if (window.scrollY >= 50) {
        ind.classList.add('disabled');
    } else {
        ind.classList.remove('disabled');
    }
}

window.addEventListener('scroll', scrollIndicator);
