// 1. Clave única para guardar nuestras reservas en el navegador
const CLAVE_LOCALSTORAGE = "reservas_salas";
// 2. Comprobar que nuestro JS responde cuando se carga la página
document.addEventListener("DOMContentLoaded", function() {
  console.log("¡El archivo gestionReservas.js se ha cargado correctamente!");
});
// 3. Función para guardar una nueva reserva en el navegador
function guardarReserva(nuevaReserva) {
  // Leemos las reservas que ya existen en localStorage (o un array vacío si no hay ninguna)
  let reservasGuardadas = JSON.parse(localStorage.getItem(CLAVE_LOCALSTORAGE)) || [];

  // Añadimos la nueva reserva al array
  reservasGuardadas.push(nuevaReserva);

  // Convertimos el array a texto y lo guardamos
  localStorage.setItem(CLAVE_LOCALSTORAGE, JSON.stringify(reservasGuardadas));
  
  console.log("¡Reserva guardada con éxito en localStorage!");
}
// 4. Función para leer de localStorage y mostrar las reservas en la página
function mostrarReservasGuardadas() {
  // Obtenemos el texto guardado y lo reconvertimos a objeto/array
  const datos = localStorage.getItem(CLAVE_LOCALSTORAGE);
  const reservas = JSON.parse(datos) || [];

  // Buscamos el contenedor donde queremos mostrarlas (si no existe, no hace nada)
  const contenedor = document.getElementById("lista-reservas");
  if (!contenedor) return;

  // Limpiamos el contenido anterior
  contenedor.innerHTML = "";

  // Recorremos las reservas y creamos un elemento HTML por cada una
  reservas.forEach(function(reserva) {
    const tarjeta = document.createElement("p");
    tarjeta.textContent = `📌 Reserva a nombre de: ${reserva.nombre} | Fecha: ${reserva.fecha}`;
    contenedor.appendChild(tarjeta);
  });
}
// 5. Se ejecuta automáticamente cuando la página termina de cargar
document.addEventListener("DOMContentLoaded", function() {
  console.log("¡El archivo gestionReservas.js se ha cargado correctamente!");

  // Creamos una reserva de prueba para ver que la lógica funciona
  const reservaPrueba = {
    nombre: "Alejandra (Prueba)",
    fecha: "2026-10-15"
  };

  // Guardamos la prueba y mostramos lo que hay en localStorage
  guardarReserva(reservaPrueba);
  mostrarReservasGuardadas();
});