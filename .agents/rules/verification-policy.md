# Verification Policy / Regla de Verificación

- **Prohibición de ejecución de verificaciones en consola por el agente:** El agente NO debe ejecutar comandos en la consola para realizar verificaciones, pruebas o chequeos automáticos (p. ej., `php artisan test`, `phpunit`, `tinker`, scripts de comprobación o peticiones curl de validación).
- **Verificación exclusiva por el desarrollador:** Toda verificación, ejecución de pruebas o comprobación de funcionamiento debe ser realizada directamente por el programador utilizando los comandos nativos del framework.
- **Instrucciones al usuario:** Cuando una modificación o desarrollo requiera comprobación, el agente debe proporcionar e indicar al programador el comando nativo exacto (por ejemplo: `php artisan test --filter=...`, `php artisan tinker`, etc.) para que este lo ejecute en su terminal.
