//creado por enri (corregido: no falla si no existen los filtros y solo afecta a las tarjetas de salones)
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchInput');
  const capacityFilter = document.getElementById('capacityFilter');

  // Si la página no tiene los filtros, no hacemos nada (evita el error en consola)
  if (!searchInput || !capacityFilter) return;

  function filterCards() {
    // Las tarjetas se generan dinámicamente, así que se buscan en cada filtrado
    const cards = document.querySelectorAll('.card-salon');
    const searchTerm = searchInput.value.toLowerCase();
    const capacityValue = capacityFilter.value;

    cards.forEach(card => {
      const title = card.querySelector('.etiqueta').textContent.toLowerCase();
      const capacity = parseInt(card.getAttribute('data-capacity'));

      const matchesSearch = title.includes(searchTerm);
      let matchesCapacity = true;

      if (!isNaN(capacity)) {
        if (capacityValue === '80') matchesCapacity = capacity <= 100;
        if (capacityValue === '200') matchesCapacity = capacity > 100;
      }

      card.style.display = (matchesSearch && matchesCapacity) ? 'flex' : 'none';
    });
  }

  searchInput.addEventListener('input', filterCards);
  capacityFilter.addEventListener('change', filterCards);
});