/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/about.css';
import './styles/home.css';
import './styles/events.css';
import './styles/gallery.css';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";
import "./styles/about.css";
import "./styles/home.css";
import "./styles/events.css";
import "./styles/gallery.css";
import "./styles/admin.css";

document.addEventListener("DOMContentLoaded", function () {
    // Animation des cartes statistiques
    const statsCards = document.querySelectorAll(".stats-card");
    statsCards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        setTimeout(() => {
            card.style.transition = "all 0.5s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, index * 100);
    });

    // Animation des lignes du tableau
    const tableRows = document.querySelectorAll(".custom-table tbody tr");
    tableRows.forEach((row, index) => {
        row.style.opacity = "0";
        row.style.transform = "translateX(-20px)";
        setTimeout(() => {
            row.style.transition = "all 0.5s ease";
            row.style.opacity = "1";
            row.style.transform = "translateX(0)";
        }, index * 50 + 300);
    });
});

// Amélioration du feedback utilisateur
document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", function (e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn && !this.onsubmit) {
            submitBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin me-1"></i>Traitement...';
            submitBtn.disabled = true;
        }
    });
});
