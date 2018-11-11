const nav = document.querySelector('#navbar');
const navTop = nav.offsetTop;

function stickyNavigation() {
    // console.log('navTop = ' + navTop);
    // console.log('scrollY = ' + window.scrollY);

    if (window.scrollY >= navTop) {
        // nav offsetHeight = height of nav
        document.body.style.paddingTop = nav.offsetHeight + 'px';
        nav.classList.add('fixed');
        console.log("Unstick: " + nav.classList);
    } else {
        document.body.style.paddingTop = 0;
        nav.classList.remove('fixed');
        console.log("Stick: " + nav.classList);
    }
}

window.addEventListener('scroll', stickyNavigation);
