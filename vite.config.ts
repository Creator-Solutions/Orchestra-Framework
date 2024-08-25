import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";

export default defineConfig({
  base: "/", // The base URL for serving assets
  plugins: [react()],
  root: path.resolve(__dirname, "./app/resources/js"),
  build: {
    outDir: path.resolve(__dirname, "./public/assets"),
    emptyOutDir: true, // Ensure the directory is cleared before building
    assetsDir: "", // No additional subdirectory for assets
    rollupOptions: {
      input: path.resolve(__dirname, "./app/resources/js/index.tsx"),
    },
  },
  server: {
    port: 5173, // Ensure this matches the port in your script tag
    open: false,
    strictPort: true, // Ensure the port is strictly used
  },
});
