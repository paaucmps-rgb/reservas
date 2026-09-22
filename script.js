//creado por enri
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchInput');
  const capacityFilter = document.getElementById('capacityFilter');
  const cards = document.querySelectorAll('.card');

  function filterCards() {
    const searchTerm = searchInput.value.toLowerCase();
    const capacityValue = capacityFilter.value;

    cards.forEach(card => {
      const title = card.querySelector('h3').textContent.toLowerCase();
      const capacity = parseInt(card.getAttribute('data-capacity'));

      const matchesSearch = title.includes(searchTerm);
      let matchesCapacity = true;

      if (capacityValue === '80') matchesCapacity = capacity <= 100;
      if (capacityValue === '200') matchesCapacity = capacity > 100;

      if (matchesSearch && matchesCapacity) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }

  searchInput.addEventListener('input', filterCards);
  capacityFilter.addEventListener('change', filterCards);
});