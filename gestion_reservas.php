<?php ?>
document.addEventListener("DOMContentLoaded", function () {
  console.log("¡El archivo gestionReservas.js se ha cargado correctamente!");
  mostrarReservasGuardadas();
});

// Se llama desde el formulario al enviarlo (ver index.html)
function manejarEnvioReserva(event) {
  event.preventDefault();

  const reserva = {
    salon: document.getElementById("salon-reserva").value,
    nombre: document.getElementById("nombre").value,
    fecha: document.getElementById("fecha").value,
    franja: document.getElementById("franja").value
  };

  guardarReserva(reserva);
  event.target.reset();
  return false;
}

// Envía la reserva al servidor (PHP) para que la guarde en la base de datos
function guardarReserva(nuevaReserva) {
  fetch("guardar_reserva.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(nuevaReserva)
  })
    .then(function (respuesta) { return respuesta.json(); })
    .then(function (datos) {
      if (datos.exito) {
        alert("¡Reserva registrada con éxito!");
        mostrarReservasGuardadas();
      } else {
        alert("No se pudo guardar la reserva: " + datos.mensaje);
      }
    })
    .catch(function (error) {
      console.error("Error al guardar la reserva:", error);
      alert("Ocurrió un error al conectar con el servidor.");
    });
}

// Pide al servidor la lista de reservas guardadas y las muestra en pantalla
function mostrarReservasGuardadas() {
  const contenedor = document.getElementById("lista-reservas");
  if (!contenedor) return;

  fetch("listar_reservas.php")
    .then(function (respuesta) { return respuesta.json(); })
    .then(function (reservas) {
      contenedor.innerHTML = "";
      reservas.forEach(function (reserva) {
        const tarjeta = document.createElement("p");
        tarjeta.textContent = `📌 ${reserva.nombre} | Salón: ${reserva.salon} | Fecha: ${reserva.fecha} | Franja: ${reserva.franja} | Estado: ${reserva.estado}`;
        contenedor.appendChild(tarjeta);
      });
    })
    .catch(function (error) {
      console.error("Error al obtener las reservas:", error);
    });
}