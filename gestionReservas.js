// Creado por Alejandra Carmona

const CLAVE_LOCALSTORAGE = "reservas_salas";

// Comprobar que el JS se carga correctamente
document.addEventListener("DOMContentLoaded", function() {
  console.log("¡El archivo gestionReservas.js se ha cargado correctamente!");
});

// Guardar una reserva en localStorage
function guardarReserva(nuevaReserva) {
  let reservasGuardadas =
    JSON.parse(localStorage.getItem(CLAVE_LOCALSTORAGE)) || [];

  reservasGuardadas.push(nuevaReserva);

  localStorage.setItem(
    CLAVE_LOCALSTORAGE,
    JSON.stringify(reservasGuardadas)
  );

  console.log("¡Reserva guardada con éxito en localStorage!");
}

// Enviar los datos del usuario a guardar_reserva.php
async function enviarUsuario() {

  const nombre = document.getElementById("nombre").value;
  const apellidos = document.getElementById("apellidos").value;
  const email = document.getElementById("email").value;

  const datosUsuario = {
    nombre: nombre,
    apellidos: apellidos,
    email: email
  };

  try {

    const respuesta = await fetch("guardar_reserva.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(datosUsuario)
    });

    const resultado = await respuesta.json();

    if (!respuesta.ok) {
      console.error(resultado.error);
      return;
    }

    console.log("Usuario guardado correctamente:", resultado);

  } catch (error) {
    console.error("Error al conectar con el servidor:", error);
  }
}