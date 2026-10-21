# Estado de la entrega · 21 de octubre de 2026

## Preparado y comprobado

- Código integrado en la tienda original y desarrollado en la rama local `feature/login` desde `master`.
- SQL completo generado con `mysqldump`, con dos usuarios ficticios, tres direcciones, tres pedidos y cuatro líneas. Reimportado en una segunda base vacía con los recuentos correctos.
- Memoria técnica PDF con capturas reales, explicación del desarrollo, decisiones, pruebas y uso de IA.
- Pruebas de navegador y de base de datos incluidas con resultados.

## Pendiente en GitHub

No se ha publicado la rama ni se ha abierto una Pull Request. El conector de GitHub informa acceso de lectura (`push: false`); la comprobación de publicación con Git no pudo autenticarse en este entorno. Ninguno de esos pasos se presenta como realizado.

Para terminar este requisito, usa una sesión con acceso de escritura al repositorio, ejecuta `git push -u origin feature/login` y abre la PR contra `master`. La descripción está preparada en `docs/PULL_REQUEST.md`.

Falta facilitar el usuario de GitHub del profesor y seleccionarlo como **Reviewer**. No confundir reviewer con assignee. No se ha fusionado ninguna rama.

## Antes de presentar

Lee la memoria y revisa el código, especialmente las consultas preparadas, el ID guardado en sesión y el autocompletado del checkout. La memoria declara el uso de IA; debe acompañarse de tu comprensión personal del proyecto y de la PR publicada.
