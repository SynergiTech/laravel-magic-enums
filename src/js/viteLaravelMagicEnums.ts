import chokidar, { type FSWatcher, type ChokidarOptions } from 'chokidar';
import type { Plugin } from 'vite';
import { artisan } from './utils';

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
  /**
   * Whether to format the generated file with `--format` flag.
   * @default false
   */
  format?: boolean;
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

export function laravelMagicEnums(options: PluginOptions): Plugin {
  let fsWatcher: FSWatcher | null = null;

  const pluginConfig = {
    input: options.input ?? 'app/Enums',
    output: options.output ?? 'resources/js/laravel-magic-enums/enums.js',
    prettier: options.prettier,
    format: options.format ?? false,
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

    let command = `laravel-magic-enums:generate \
      --input=${pluginConfig.input}
      --output=${pluginConfig.output}`;

    if (pluginConfig.format) {
      command += `--format`;
    }

    if (pluginConfig.prettier) {
      command += `--prettier="${pluginConfig.prettier}"`;
    }

    artisan(command);

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
