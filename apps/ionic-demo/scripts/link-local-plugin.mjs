/**
 * Capacitor resolves native plugins from this app's node_modules. In an npm workspace
 * the dependency may only exist at the repo root — create a symlink so `npx cap sync` works.
 */
import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const appRoot = path.resolve(scriptDir, "..");
const linkPath = path.join(appRoot, "node_modules", "@hybrid", "capacitor-hybrid-updater");
const target = path.resolve(appRoot, "../../packages/capacitor-hybrid-updater");

if (fs.existsSync(linkPath)) {
  process.exit(0);
}

fs.mkdirSync(path.dirname(linkPath), { recursive: true });
if (process.platform === "win32") {
  fs.symlinkSync(target, linkPath, "junction");
} else {
  fs.symlinkSync(target, linkPath, "dir");
}
