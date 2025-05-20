import { defineConfig, type UserConfig } from 'vite';
import { resolve } from 'node:path';
import dts from 'vite-plugin-dts';
import { fileURLToPath, URL } from 'node:url';

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  const baseConfig: UserConfig = {
    plugins: (function () {
      if (mode === 'package') {
        return [
          dts({
            entryRoot: 'src/js/',
            tsconfigPath: './tsconfig.json',
          }),
        ];
      }
      return [];
    })(),
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('src/js', import.meta.url)),
      },
    },
  };

  if (mode === 'package') {
    return {
      ...baseConfig,
      build: {
        outDir: 'dist',
        emptyOutDir: true,
        lib: {
          entry: {
            useEnums: resolve('src/js/useEnums.ts'),
            viteLaravelMagicEnums: resolve('src/js/viteLaravelMagicEnums.ts'),
          },
          formats: ['es', 'cjs'],
        },
        rollupOptions: {
          external: ['fs', 'path', 'chokidar', 'child_process'],
        },
      },
    };
  }

  // For testing, etc.
  return {
    ...baseConfig,
    build: {
      outDir: './docs',
      emptyOutDir: true,
    },
  };
});
