// Adiciona a classe "show" ao dropdown e ao link que o aciona
$(document).ready(function() {
    // Adiciona a classe "show" ao dropdown
    $(".dropdown-menu").addClass("show");
    // Adiciona a classe "show" ao link que aciona o dropdown
    $(".nav-link").attr("aria-expanded", "true");
});
