---
description: Guarda los cambios del proyecto — formatea, corre tests y hace commit
argument-hint: "mensaje del commit (opcional)"
---

Guarda los cambios actuales del proyecto siguiendo estos pasos en orden:

## 1. Revisar qué cambió

```bash
git status
git diff
```

Muéstrame un resumen de los archivos modificados.

## 2. Formatear el código

```bash
vendor/bin/sail bin pint --dirty --format agent
```

## 3. Correr todos los tests

```bash
vendor/bin/sail artisan test --compact
```

Si algún test falla, detente y reporta el error antes de continuar.

## 4. Hacer el commit

Usa los archivos modificados (no `git add -A`) y redacta el mensaje basándote en los cambios reales.

Si el usuario proporcionó un mensaje en `$ARGUMENTS`, úsalo. Si no, redacta uno descriptivo en español basado en los cambios.

```bash
git add <archivos modificados específicos>
git commit -m "$(cat <<'EOF'
<mensaje del commit>

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
EOF
)"
```

## 5. Confirmar

Muestra el resultado de `git log --oneline -3` para confirmar que el commit quedó registrado.
