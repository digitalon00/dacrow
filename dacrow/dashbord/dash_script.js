function showproducts() {
    let productgrids = document.querySelector(".products-grids");
    let adminsgrids = document.querySelector(".admins-grids");
    if (productgrids.style.display === "block") {
        productgrids.style.display = "none";
    } else {
        productgrids.style.display = "block";
        adminsgrids.style.display="none"
    }
}

function showadmins() {
    let adminsgrids = document.querySelector(".admins-grids");
    let productgrids = document.querySelector(".products-grids");
    if (adminsgrids.style.display === "block") {
        adminsgrids.style.display = "none";
    } else {
        adminsgrids.style.display = "block";
        productgrids.style.display="none"
    }
}

function hidemenu() {
    let menu = document.querySelector(".menu");
    let dash_cont = document.querySelector(".dashboard-container");
    let border = document.querySelector(".border");
    let hideicon = document.querySelector(".menu-dash-icon");

    if (menu.style.transform==="translateX(-100%)") {
        border.style.left="20%";
        menu.style.transform="translateX(0)";
        dash_cont.style.left="20%";
        dash_cont.style.width="80%";
        hideicon.style.left="20%";
        hideicon.setAttribute("name","caret-back");
    }else{
        border.style.left="0";
        menu.style.transform="translateX(-100%)";
        dash_cont.style.left="0";
        dash_cont.style.width="100%";
        hideicon.style.left="0";
        hideicon.setAttribute("name","caret-forward");
    }
}
