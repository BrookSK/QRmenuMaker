class mainJS {
    constructor() {
      this.onDOM();
    }
    onDOM() {
      this.togglePreWrapper();
      this.carousel();
      this.accordion();
      this.dropdown();
      this.tab();
      this.mobileMenu();
      this.tooltip();
    }
    tooltip(){
      $('[data-toggle="tooltip"]').tooltip();
    }
    mobileMenu() {
      document.addEventListener("click", (e) => {
        if (
          e.target.matches(".show-mobile-menu") ||
          e.target.matches(".close-mobile-menu")
        ) {
          document.body.classList.toggle("mobile-menu-opened");
        }
      });
      document.querySelector("#mobile-menu").addEventListener("click", (e) => {
        if (e.target.matches("#mobile-menu")){
          document.body.classList.toggle("mobile-menu-opened");
        }
      });
      document.querySelectorAll("#mobile-menu .item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
          if (e.target.matches(".toggle-submenu")) {
            e.preventDefault();
            item.classList.toggle("expanded");
          }
        });
      });
      
    }
    tab() {
      document.addEventListener("click", (e) => {
        if (e.target.matches(".menu-tab")) {
          let href = e.target.getAttribute("href");
          document.querySelectorAll(".content-tab.expanded").forEach((item) => {
            item.classList.remove("expanded");
          });
          document.querySelector(href).classList.add("expanded");
        }
      });
    }
    dropdown() {
      document.addEventListener("click", (e) => {
        if (e.target.closest(".dropdown")) {
          if (e.target.classList.contains("dropdown-toggle"))
            e.target.parentElement.classList.toggle("expanded");
        } else {
          document.querySelectorAll(".dropdown.expanded").forEach((item) => {
            item.classList.remove("expanded");
          });
        }
      });
    }
    togglePreWrapper() {
      document.addEventListener("click", (e) => {
        if (e.target.matches(".toggle-pre-wrapper")) {
          document.body.classList.toggle("pre-wrapper-opened");
        }
      });
    }
    accordion() {
      document.querySelectorAll(".accordion").forEach((item) => {
        item.addEventListener("click", (e) => {
          if (e.target.classList.contains("collapse"))
            item.classList.toggle("expanded");
        });
      });
    }
    carousel() {
      document
        .querySelectorAll(".carousel > .carousel_items")
        .forEach((slider) => {
          tns({
            mouseDrag: 1,
            container: slider,
            controlsContainer:
              typeof slider.getAttribute("data-controlsContainer") !== null
                ? slider.getAttribute("data-controlsContainer")
                : 0,
            items:
              slider.getAttribute("data-items") !== null
                ? Number(slider.getAttribute("data-items"))
                : 1,
            slideBy: "page",
            loop:
              slider.getAttribute("data-loop") !== null
                ? Number(slider.getAttribute("data-loop"))
                : 0,
            controls:
              typeof slider.getAttribute("data-controls") !== null
                ? Number(slider.getAttribute("data-controls"))
                : 0,
            nav:
              slider.getAttribute("data-nav") !== null
                ? Number(slider.getAttribute("data-nav"))
                : 0,
            navPosition: "bottom",
            autoplayResetOnVisibility: true,
            responsive: {
              576: slider.getAttribute("data-responsive-576")
                ? JSON.parse(slider.getAttribute("data-responsive-576"))
                : { items: 2, gutter: 16 },
              768: slider.getAttribute("data-responsive-768")
                ? JSON.parse(slider.getAttribute("data-responsive-768"))
                : { items: 3, gutter: 16 },
              1200: slider.getAttribute("data-responsive-1200")
                ? JSON.parse(slider.getAttribute("data-responsive-1200"))
                : { items: 3, gutter: 24 },
              1600: slider.getAttribute("data-responsive-1600")
                ? JSON.parse(slider.getAttribute("data-responsive-1600"))
                : { items: 4, gutter: 32 },
            },
          });
        });
    }
  }
  document.addEventListener(
    "DOMContentLoaded",
    () => {
      new mainJS();
    },
    false
  );
  