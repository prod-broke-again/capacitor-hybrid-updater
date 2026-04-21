# ionic-demo

Демо **Ionic React + Capacitor** для `@hybrid/capacitor-hybrid-updater`.

Инструкции и контекст: [README.md](../../README.md) · [README.en.md](../../README.en.md)

## Кратко

```bash
# из корня монорепо
npm install
cd apps/ionic-demo
npm run dev          # браузер
npm run build && npx cap sync android   # нативная сборка
```

`.env`: `VITE_LARAVEL_BASE_URL` — для эмулятора часто `http://10.0.2.2:8080`.
