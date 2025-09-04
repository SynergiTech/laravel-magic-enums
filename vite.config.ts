import { type UserConfig } from 'vite';
import { resolve } from 'node:path';
import dts from 'vite-plugin-dts';
import { fileURLToPath, URL } from 'node:url';
import { defineConfig, configDefaults } from 'vitest/config';

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
            viteLaravelMagicEnums: resolve('src/js/viteLaravelMagicEnums.ts'),
          },
          formats: ['es', 'cjs'],
        },
        rollupOptions: {
          external: ['node:fs', 'node:path', 'node:child_process', 'chokidar'],
        },
      },
    };
  }
  baseConfig.test = {
    exclude: [...configDefaults.exclude],
    coverage: {
      provider: 'v8',
      include: ['src/js'],
      /*  thresholds: {
          branches: 50,
          functions: 50,
          lines: 50,
          statements: 50,
        }, */
    },
  };

  // For testing, etc.
  return {
    ...baseConfig,
    build: {
      outDir: './docs',
      emptyOutDir: true,
    },
  };
});
