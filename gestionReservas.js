// Creado por Alejandra Carmona

const CLAVE_LOCALSTORAGE = "reservas_salas";

// Comprobar que el JS se carga correctamente
document.addEventListener("DOMContentLoaded", function() {
  console.log("¡El archivo gestionReservas.js se ha cargado correctamente!");
  mostrarReservasGuardadas();
});

// Se llama desde el formulario al enviarlo (ver index.html)
function manejarEnvioReserva(event) {
  event.preventDefault();

  const formulario = event.target;
  // Buscamos el botón de envío dentro del formulario (puedes ajustar el selector si tu botón tiene una clase o ID específico)
  const botonSubmit = formulario.querySelector('button[type="submit"]') || formulario.querySelector('input[type="submit"]');

  const reserva = {
    salon: document.getElementById("salon-reserva").value,
    nombre: document.getElementById("nombre").value,
    fecha: document.getElementById("fecha").value,
    franja: document.getElementById("franja").value
  };

  // 1. Desactivar el botón mientras se guarda para evitar peticiones dobles
  if (botonSubmit) {
    botonSubmit.disabled = true;
  }

  // 2. Llamar a guardarReserva y manejar el resultado asíncrono
  guardarReserva(reserva)
    .then(function (guardada) {
      // 3. Vaciar el formulario SOLO cuando el servidor confirme que se guardó con éxito
      if (guardada) {
        formulario.reset();
      }
    })
    .finally(function () {
      // 4. Reactivar el botón pase lo que pase (éxito o error)
      if (botonSubmit) {
        botonSubmit.disabled = false;
      }
    });

  return false;
}

// Envía la reserva al servidor (PHP) y devuelve una promesa con un booleano (true/false)
function guardarReserva(nuevaReserva) {
  return fetch("guardar_reserva.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(nuevaReserva)
  })
    .then(function (respuesta) { return respuesta.json(); })
    .then(function (datos) {
      if (datos.exito) {
        alert("¡Reserva registrada con éxito!");
        mostrarReservasGuardadas();
        return true; // Indicamos que se guardó correctamente
      } else {
        alert("No se pudo guardar la reserva: " + datos.mensaje);
        return false; // Indicamos que falló para no vaciar el formulario
      }
    })
    .catch(function (error) {
      console.error("Error al guardar la reserva:", error);
      alert("Ocurrió un error al conectar con el servidor.");
      return false; // En caso de error de red tampoco se vacía
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