function stickyNav(navID, menuHeight) {

  let navbar = document.getElementById(navID);

  if (navbar) {

    navbar.classList.add("d-none");

    if ( !menuHeight ) {
      menuHeight = navbar.offsetHeight;
    } else {
      menuHeight = Number(menuHeight);
    }
    
    menuHeight = menuHeight + 200;

    // make nav sticky
    window.addEventListener("scroll", () => {

      if (window.scrollY === 0) {
        navbar.classList.remove("fixed-top");
        navbar.classList.add("d-none");
      } else if (window.scrollY > menuHeight) {
        navbar.classList.add("fixed-top");
        navbar.classList.add("animated");
        navbar.classList.add("slideDown");
        navbar.classList.add("d-block");
        navbar.classList.remove("slideUp");
        navbar.classList.remove("d-none");
      } else if (window.scrollY < menuHeight) {
        navbar.classList.add("slideUp");
        navbar.classList.remove("d-block");
        navbar.classList.remove("slideDown");
      } else if (window.scrollY === 0) {
        navbar.classList.remove("fixed-top");
      }

    });    

  } else {
    console.log('cannot find menu');
  }
}