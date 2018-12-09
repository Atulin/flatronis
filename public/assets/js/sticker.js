const nav = document.getElementById('navbar');
const navTop = nav.offsetTop;

function stickyNavigation() {

    if (window.scrollY >= navTop) {
        document.body.style.paddingTop = nav.offsetHeight + 'px';
        nav.classList.add('fixed');
        console.log("Navbar unstick: " + nav.classList);
    } else {
        document.body.style.paddingTop = 0;
        nav.classList.remove('fixed');
        console.log("Navbar stick: " + nav.classList);
    }
}

window.addEventListener('scroll', stickyNavigation);
