let alumnos = [
    {
        nombre: "Juan",
        apellido: "Perez",
        NIF: "12345678x",
        fecha_Nacimiento: "1990/01/01",
        lugar_Nacimiento: "Madrid",
        nota_media: 7
    },
    {
        nombre: "mateo",
        apellido: "Gonzalez",
        NIF: "87654321y",
        fecha_Nacimiento: "1992/05/15",
        lugar_Nacimiento: "Barcelona",
        nota_media: 8
    },
    {
        nombre: "Lucia",
        apellido: "Martinez",
        NIF: "56789012z",
        fecha_Nacimiento: "1993/08/20",
        lugar_Nacimiento: "Valencia",
        nota_media: 9
    },
    {
        nombre: "Carlos",
        apellido: "Lopez",
        NIF: "34567890a",
        fecha_Nacimiento: "1991/12/10",
        lugar_Nacimiento: "Sevilla",
        nota_media: 6
    },
    {
        nombre: "Ana",
        apellido: "Rodriguez",
        NIF: "90123456b",
        fecha_Nacimiento: "1994/03/25",
        lugar_Nacimiento: "Bilbao",
        nota_media: 2
    }
];

let frutas = ["Manzana", "Platano", "Naranja", "Pera", "Uva"];

// crearia un array de frutas, y asignaria cada fruta a cada alumno con una funcicón 

function asignarFrutas() {
    alumnos.forEach((alumno, index) => {
        alumno.frutaasignada =frutas [index];
    });

 console.table (alumnos, {"nombre":"fruta asignada"});

};

 asignarFrutas();

 // Obtener elementos del DOM
const emailInput = document.getElementById("email");
const phoneInput = document.getElementById("phone");
const messageText = document.getElementById("message");
const submitButton = document.getElementById("submitButton");


// Cuando el usuario hace clic en el campo email
emailInput.addEventListener("focus", function () {
    phoneInput.disabled = false;
    messageText.disabled = false;
});


// Cuando escribe en el campo email
emailInput.addEventListener("input", function () {

    if (emailInput.value.trim() !== "") {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }
});