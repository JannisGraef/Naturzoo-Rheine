document.addEventListener("DOMContentLoaded", () => {
    // Swoosh-Effekt für die Info-Karten
    const cards = document.querySelectorAll('.intro-cards .card');
    cards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.classList.add('hovered');
      });
      card.addEventListener('mouseleave', () => {
        card.classList.remove('hovered');
      });
    });
  });
  