# Tienda Deportiva JR · Gestión de clientes

Ampliación del catálogo original con PHP nativo, sesiones y MySQL/MariaDB. Mantiene los seis productos, imágenes, Bootstrap y colores del proyecto. El repositorio de partida (`c8e2905`, rama `master`) solo contenía HTML/CSS/JS: también se ha implementado el carrito y el checkout necesarios para la práctica.

## Arranque con XAMPP

1. Requisitos: PHP 8.2 o superior con `pdo_mysql` y `mbstring`; MySQL 8.0.16+ o MariaDB 10.4+; navegador. No requiere Composer ni un framework PHP.
2. Copia la carpeta en `C:/xampp/htdocs/tienda_deportivaJR` y arranca Apache y MySQL.
3. En phpMyAdmin, importa **solo** `database/tienda_deportiva_jr.sql`. Es un volcado real de estructura y datos ficticios, generado con `mysqldump` y codificación UTF-8. Usa una base nueva para evitar colisiones con tablas existentes. Alternativamente importa `esquema.sql` y después `datos-prueba.sql`; no combines ambas opciones.
4. Copia `config/local.example.php` como `config/local.php`. Ajusta puerto, usuario y contraseña. El puerto habitual de XAMPP es `3306`.
5. Abre `http://localhost/tienda_deportivaJR/`. También funciona `tiendadeportiva.html`, que enlaza a la entrada PHP.

Alternativa sin Apache, desde la carpeta del proyecto:

```powershell
C:/xampp/php/php.exe -S 127.0.0.1:8085 -t .
```

Abre `http://127.0.0.1:8085/`. MySQL debe seguir arrancado. Nunca abras los PHP con `file://` ni con Live Server de VS Code.

## Cuentas de demostración

| Cuenta | Contraseña | Datos |
| --- | --- | --- |
| ana@example.com | DemoJR2026! | Dirección de entrega, facturación y pedido de 45 € |
| bruno@example.com | DemoJR2026! | Dirección propia y pedido de 120 € |

Existe además un pedido de invitado de 8 €. Los datos son ficticios; estas contraseñas son públicas y exclusivas de la demostración. La base guarda hashes, no las contraseñas.

## Recorrido de comprobación manual

1. Sin iniciar sesión, visita `mi-cuenta.php`: redirige al login.
2. Crea una cuenta; prueba correo duplicado, contraseña corta y confirmación distinta.
3. Inicia sesión como Ana; verás solo sus direcciones y pedidos.
4. Añade una dirección de entrega y marca la casilla de principal. Las direcciones de facturación no sustituyen las de entrega.
5. Añade un producto al carrito y entra al checkout. Se rellenan nombre, correo y dirección de entrega principal.
6. Confirma el pedido ficticio y vuelve a Mi Cuenta. Aparece en el historial. El carrito queda vacío.
7. Cierra sesión e inicia como Bruno: el pedido de Ana no aparece.
8. Cierra sesión, añade otro producto y entra como invitado. Los campos están vacíos; al confirmar, el pedido tiene `usuario_id = NULL`.

## Estructura y decisiones

- `config/`: conexión PDO reutilizable; configuración local excluida de Git.
- `includes/bootstrap.php`: sesiones, CSRF, escape de salida, validaciones y catálogo con precios del servidor en céntimos.
- `includes/header.php` y `footer.php`: navegación y diseño compartidos.
- `registro.php`, `login.php`, `logout.php`: hash/verificación de contraseñas y renovación/destrucción de sesión.
- `mi-cuenta.php`: alta y listado de direcciones; historial filtrado por el usuario de la sesión.
- `carrito.php`, `checkout.php`, `pedido-confirmado.php`: carrito en sesión, pedidos y líneas en transacción, resumen y confirmación.
- `database/`: esquema, semilla de prueba y volcado completo para entregar.
- `docs/`: memoria PDF, capturas y resultados reales de pruebas; `tests/`: comprobaciones reproducibles.

Se usan consultas preparadas, claves foráneas, `DECIMAL` para dinero y copia de los datos de envío en cada pedido. Cambiar una dirección no altera pedidos antiguos. La primera dirección de cada tipo se vuelve principal; la casilla permite sustituirla al crear otra. Un bloqueo de la fila del usuario durante el alta evita dos principales simultáneas a través de la aplicación. El checkout no guarda automáticamente cambios en la agenda.

Todas las mutaciones llevan CSRF. Logout solo acepta POST. Tras guardar se redirige con HTTP 303. Un token de checkout de un solo uso, único en base de datos, ayuda a impedir compras duplicadas. La sesión se renueva al entrar y la cookie usa HttpOnly, SameSite=Lax y Secure cuando se sirve por HTTPS.

Alcance académico: envío nacional (CP de cinco cifras), sin cobros reales, gestión de stock, recuperación de contraseña, verificación de email ni limitación persistente de intentos. Para publicar una tienda real faltarían esas funciones y HTTPS. El formulario de contacto original sigue utilizando su servicio externo; no forma parte de las pruebas y no se ha enviado ningún mensaje.

## Pruebas automatizadas

Usa una **base de prueba nueva** con los datos iniciales. Las pruebas E2E crean una cuenta, direcciones y dos pedidos ficticios. Necesitas Node.js, Playwright y Chrome instalado:

```powershell
npm install --no-save playwright
$env:BASE_URL = 'http://127.0.0.1:8085'
node tests/e2e.cjs
C:/xampp/php/php.exe tests/database.php
```

Las capturas se guardan en `docs/capturas/`; los resultados de navegador en `docs/resultados-pruebas.json`. El script SQL se verificó mediante importación en una segunda base vacía. Las pruebas ejecutadas se explican en la memoria. Para repetirlas, restaura la base de prueba a sus datos iniciales; no las ejecutes sobre datos reales.

## Entrega y Git

Rama de desarrollo: **`feature/login`**, destino **`master`** (nombre real de la rama principal). El PDF y el SQL están incluidos en el repositorio.

Para publicar la copia local con una cuenta que tenga acceso de escritura:

```powershell
git switch feature/login
git push -u origin feature/login
```

Después abre una Pull Request `feature/login` → `master`, copia la descripción de `docs/PULL_REQUEST.md` y selecciona el usuario del profesor en **Reviewers**. No fusiones la PR antes de la revisión. Consulta `docs/ENTREGA.md` para conocer el estado de publicación de esta entrega.

## Uso de IA

Implementación y documentación asistidas por Codex. La memoria identifica las peticiones reales, las decisiones auditadas, las correcciones y las comprobaciones realizadas. El estudiante debe revisar el código y entenderlo antes de entregar; las pruebas automáticas no sustituyen esa revisión personal.
