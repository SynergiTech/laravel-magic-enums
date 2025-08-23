import { execSync } from 'child_process';
import chokidar, { type FSWatcher, type ChokidarOptions } from 'chokidar';
import type { Plugin } from 'vite';
import path from 'node:path';

interface PluginOptions {
  /**
   * The directory to watch for changes.
   * @default app/Enums
   */
  input?: string;
  /**
   * The output file for the enum interface.
   * @default magic-enums.d.ts
   */
  output?: string;
  /**
   * Additional options to pass to chokidar.
   */
  chokidarOptions?: ChokidarOptions;
  /**
   * The command to run prettier and format the enum export. A value of `undefined` will not run prettier.
   * @default undefined
   */
  prettier?: string;
}

const defaultChokidarOptions: ChokidarOptions = {
  ignoreInitial: true,
  atomic: false,
  awaitWriteFinish: {
    pollInterval: 100,
  },
  persistent: false,
  interval: 300,
};

const testbenchDir = path.join(__dirname, 'vendor', 'bin', 'testbench');

function artisan(command: string): void {
  console.error(execSync(`${testbenchDir} ${command}`).toString('utf8'));
}

export function laravelMagicEnums(options: PluginOptions): Plugin {
  let fsWatcher: FSWatcher | null = null;

  const pluginConfig = {
    input: options.input ?? 'app/Enums',
    output: options.output ?? 'resources/js/laravel-magic-enums/enums.js',
    prettier: options.prettier,
    chokidarOptions: {
      ...defaultChokidarOptions,
      ...(options.chokidarOptions ?? {}),
    },
  } satisfies PluginOptions;

  const listenToInput = debounce(function (e: string) {
    if (e.startsWith(pluginConfig.input.slice(2))) {
      regenerate();
    }
  }, 200);

  async function regenerate() {
    console.info('Rebuilding enums file...');

    artisan(
      `laravel-magic-enums:generate \
        --input=${pluginConfig.input} \
        --output=${pluginConfig.output}`,
    );

    if (pluginConfig.prettier) {
      execSync(`${pluginConfig.prettier} --write ${pluginConfig.output}`);
    }

    console.info('... Rebuilt enums file!');
  }

  return {
    name: 'laravel-magic-enums',
    configResolved(config) {
      fsWatcher = chokidar
        .watch(pluginConfig.input, pluginConfig.chokidarOptions)
        .on('change', listenToInput)
        .on('add', listenToInput)
        .on('unlink', listenToInput);

      if (config.mode === 'development') {
        regenerate();
      }
    },

    buildEnd() {
      fsWatcher?.close();
    },
  };
}

function debounce<T extends (...args: never[]) => void>(fn: T, ms = 300) {
  let timeoutId: ReturnType<typeof setTimeout>;
  return function (this: unknown, ...args: Parameters<T>) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), ms);
  };
}
