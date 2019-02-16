const nav = document.getElementById('navbar');
const body = document.querySelector('.post-container');
const navTop = nav.offsetTop;

function stickyNavigation() {

    if (body) {
        if (window.scrollY >= navTop) {
            body.style.paddingTop = nav.offsetHeight + 'px';
            nav.classList.add('fixed');
        } else {
            body.style.paddingTop = 0;
            nav.classList.remove('fixed');
        }
    } else {
        nav.classList.add('fixed');
    }
}

window.addEventListener('scroll', stickyNavigation);
