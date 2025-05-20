import { configDefaults, defineConfig, mergeConfig } from 'vitest/config';
import viteConfig from './vite.config';

export default mergeConfig(
  viteConfig({ mode: 'test', command: 'build' }),
  defineConfig({
    test: {
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
    },
  }),
);
