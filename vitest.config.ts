import { configDefaults, defineConfig, mergeConfig } from 'vitest/config';
import viteConfig from './vite.config';

export default mergeConfig(
  viteConfig({ mode: 'test', command: 'build' }),
  defineConfig({
    test: {
      exclude: [...configDefaults.exclude],
      coverage: {
        provider: 'v8',
        include: ['src/tests/js/**/*.spec.ts'],
        thresholds: {
          branches: 100,
          functions: 100,
          lines: 100,
          statements: 100,
        },
      },
    },
  }),
);
