// -----show_hide_function-----
function showHide(section, button, displaymode) {
    button.onclick = () => {
        if (section.style.display === displaymode) {
            section.style.display = "none";
        } else {
            section.style.display = displaymode;
        }
    }
};
// -----slider_function-----
function slider(pics, firstimg, prev, next) {
    const showHideIcons = () => {
        let scrollWidth = pics.scrollWidth - pics.clientWidth;
        prev.style.display = pics.scrollLeft === 0 ? "none" : "block";
        next.style.display = pics.scrollLeft === scrollWidth ? "none" : "block";
    }

    const slideNext = () => {
        let firstimgwidth = firstimg.clientWidth + 10;
        pics.scrollLeft += firstimgwidth;
        if (pics.scrollLeft >= pics.scrollWidth - pics.clientWidth) {
            pics.scrollLeft = 0;
        }

        showHideIcons();
    };

    const startAutoSlide = () => {
        setInterval(slideNext, 3000);
    };

    prev.addEventListener("click", () => {
        let firstimgwidth = firstimg.clientWidth + 10;
        pics.scrollLeft -= firstimgwidth;
        setTimeout(() => showHideIcons(), 60);
    });

    next.addEventListener("click", () => {
        let firstimgwidth = firstimg.clientWidth + 10;
        pics.scrollLeft += firstimgwidth;
        setTimeout(() => showHideIcons(), 60);
    });

    pics.addEventListener("mouseleave", () => {
        clearInterval(autoSlideInterval);
    });

    let autoSlideInterval;
    startAutoSlide();
    showHideIcons();
}
// -----header-----
function Header() {
    //-----header_user-box-----
    let loginIcon = document.querySelector("#login-icon")
    let userBox = document.querySelector(".user-box")
    showHide(userBox, loginIcon, "block")

    //-----header_navbar_respensive-----
    if (window.innerWidth <= 767) {
        let menuIcon = document.querySelector('#menu-icon')
        let navBar = document.querySelector(".header-navbar")
        showHide(navBar, menuIcon, "flex")
        let navbarA = document.querySelectorAll(".header-navbar-a")
        navbarA.forEach(navA => {
            showHide(navBar, navA, "flex")
        });
        navBar.onmouseleave = () => {
            navBar.style.display = "none"
        }
    };
    //-----header_slider-----
    const picsSlider = document.querySelector(".slider-container"),
        firstimg = picsSlider.querySelectorAll("img")[0];
    let prevArrow = document.querySelector('.prev');
    let nextArrow = document.querySelector('.next');
    slider(picsSlider, firstimg, prevArrow, nextArrow);
};
Header();
window.addEventListener('resize', Header);

function goBack() {
    window.history.back();
}

document.addEventListener("DOMContentLoaded", function () {
    let scrollToTop = document.querySelector(".scrolltotop");
    window.onscroll = () => {
        if (document.body.scrollTop > 400 || document.documentElement.scrollTop > 400) {
            scrollToTop.style.display = "flex";
        } else {
            scrollToTop.style.display = "none";
        };
    }

    scrollToTop.onclick = () => {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0
    }
})

//search
function submitForm(e) {
    e.preventDefault();
}

