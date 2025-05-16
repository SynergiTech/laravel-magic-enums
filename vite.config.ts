import { defineConfig, type UserConfig } from "vite";
import { resolve } from "path";
import dts from "vite-plugin-dts";

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  const baseConfig: UserConfig = {
    plugins: (function () {
      if (mode === "package") {
        return [
          dts({
            entryRoot: "src/js/",
            tsconfigPath: "./tsconfig.json",
          }),
        ];
      }
      return [];
    })(),
  };

  if (mode === "package") {
    return {
      ...baseConfig,
      build: {
        outDir: "build",
        emptyOutDir: true,
        lib: {
          name: "LaravelMagicEnums",
          entry: {
            useEnums: resolve("src/js/useEnums.ts"),
            viteLaravelMagicEnums: resolve("src/js/viteLaravelMagicEnums.ts"),
          },
          formats: ["es", "cjs"],
        },
        rollupOptions: {
          external: ["fs", "path", "chokidar", "child_process"],
          globals: {
            process: "process",
          },
        },
      },
    };
  }

  // For testing, etc.
  return {
    ...baseConfig,
    build: {
      outDir: "./docs",
      emptyOutDir: true,
    },
  };
});
