## Gestión de clientes y checkout integrado

El proyecto tenía un catálogo estático y botones Comprar sin lógica. Esta ampliación permite registrarse e iniciar sesión, guardar direcciones de entrega/facturación y consultar exclusivamente los pedidos de la propia cuenta. Añade carrito y checkout para completar pedidos ficticios como invitado o con la dirección principal del usuario.

### Cambios

- PHP nativo con PDO, sesiones, `password_hash()` y `password_verify()`.
- Panel Mi Cuenta protegido y consultas filtradas por el ID de sesión.
- CSRF en formularios, escape HTML y renovación de sesión al entrar.
- Base InnoDB con claves foráneas, índice de correo único y pedidos con líneas en transacción.
- SQL completo con cuentas, direcciones y pedidos ficticios; memoria PDF con capturas y declaración del uso de IA.
- Se conservan productos, fotografías y recursos Bootstrap del proyecto original.

### Validación

Pruebas reales sobre PHP 8.2.12 y MariaDB 10.4.32: registro, duplicados, credenciales incorrectas, renovación y cierre de sesión, aislamiento entre dos cuentas, direcciones, autocompletado, checkout invitado, prevención de reenvío, CSRF, XSS y visualización móvil. Comprobación de hashes, totales, claves foráneas y vínculos de pedidos. Ver resultados y memoria en `docs/`.

### Alcance

Checkout académico sin cobros ni stock. El repositorio de partida no tenía esquema previo, por lo que se entrega un esquema inicial; no se requiere migrar tablas antiguas.

### Revisión

Seleccionar al profesor en **Reviewers** con su usuario real de GitHub. El usuario todavía no ha sido facilitado.
