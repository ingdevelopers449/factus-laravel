# Regla de Generación de Commits en Español (Git Commits Rule)

Esta regla es de **cumplimiento obligatorio** para el agente en todas las respuestas, sugerencias, preparaciones o ejecuciones de comandos de Git (`git commit`).

---

## 1. Idioma Obligatorio: Español

- **Todos** los mensajes de commit (tipo, alcance, mensaje principal y cuerpo/descripción detallada) deben estar redactados **100% en español**.
- **Prohibido el uso de inglés** en la descripción o cuerpo del commit (por ejemplo, evitar: *"fix navigation bug"*, *"add invoice model"*, *"update dependencies"*).

---

## 2. Formato: Conventional Commits en Español

La estructura debe seguir la convención estándar con descripciones claras en español:

```text
<tipo>(<alcance>): <descripción en español>
```

### Tipos Permitidos:
- **`feat`**: Nueva funcionalidad (ej: `feat(facturacion): implementar cálculo automático de impuestos`)
- **`fix`**: Corrección de un error o bug (ej: `fix(modal): corregir z-index del fondo oscuro en edición de productos`)
- **`style`**: Cambios de diseño, CSS, estilos o maquetación (ej: `style(sidebar): añadir animación y despliegue por cursor`)
- **`refactor`**: Reestructuración de código sin alterar funcionalidad (ej: `refactor(parametrizacion): limpiar campos innecesarios en formulario`)
- **`chore`**: Tareas de mantenimiento, rutas, paquetes o configuración (ej: `chore(auth): remover rutas de registro de usuarios`)
- **`docs`**: Documentación, comentarios o reglas del agente (ej: `docs(reglas): definir estándar de commits en español`)
- **`test`**: Creación o modificación de pruebas automatizadas (ej: `test(usuario): añadir pruebas de autenticación`)

---

## 3. Directrices de Redacción

1. **Verbos de acción en infinitivo o imperativo:**
   - Preferir: `agregar`, `corregir`, `actualizar`, `eliminar`, `ajustar`, `implementar`, `remover`, `configurar`.
2. **Claridad y concisión:**
   - La primera línea debe tener menos de 72 caracteres y comenzar en minúscula tras los dos puntos.
   - Sin punto final en el título del commit.
3. **Cuerpo del commit (opcional o cuando sea requerido):**
   - Siempre redactado en español explicando qué se cambió y el motivo del cambio si es complejo.

---

## 4. Ejemplos de Referencia

### ✅ Ejemplos Correctos:
- `feat(pos): agregar selección de método de pago en caja`
- `fix(productos): corregir validación de stock inicial negativo`
- `style(welcome): ajustar espaciado del encabezado y botones principales`
- `chore(storage): crear enlace simbólico para imágenes de empresa`

### ❌ Ejemplos Incorrectos:
- `feat(pos): add payment method selection` *(Prohibido: descripción en inglés)*
- `Fixed modal bug` *(Prohibido: sin formato convencional y en inglés)*
- `fix(modal): Fixed backdrop issue.` *(Prohibido: verbo en pasado y en inglés)*

