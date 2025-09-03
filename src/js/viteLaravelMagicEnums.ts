import chokidar, { type FSWatcher, type ChokidarOptions } from 'chokidar';
import type { Plugin } from 'vite';
import { execSync } from 'node:child_process';

interface PluginOptions {
  /**
   * The enums directory to watch for changes.
   * @default app/Enums
   */
  input?: string;
  /**
   * The output directory for generated files.
   */
  output?: string;
  /**
   * Whether to format the generated file with `--format` flag.
   * @default false
   */
  format?: boolean;
  /**
   * The command to run prettier and format the enum export. A value of `undefined` will not run prettier.
   * @default undefined
   */
  prettier?: string;
  /**
   * Additional options to pass to chokidar.
   */
  chokidarOptions?: ChokidarOptions;
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

function artisan(command: string): void {
  execSync(`php artisan ${command}`).toString('utf8');
}

export function laravelMagicEnums(options?: PluginOptions): Plugin {
  let fsWatcher: FSWatcher | null = null;

  const pluginConfig = {
    input: options?.input ?? 'app/Enums',
    output: options?.output ?? 'resources/js/magic-enums',
    prettier: options?.prettier ?? undefined,
    format: options?.format ?? false,
    chokidarOptions: {
      ...defaultChokidarOptions,
      ...(options?.chokidarOptions ?? {}),
    },
  } satisfies PluginOptions;

  const listenToInput = debounce(function (e: string) {
    if (e.startsWith(pluginConfig.input)) {
      regenerate();
    }
  }, 200);

  function regenerate() {
    const command: string[] = [
      `laravel-magic-enums:generate`,
      `--input="${pluginConfig.input}"`,
      `--output="${pluginConfig.output}"`,
    ];

    if (pluginConfig.format) {
      command.push(`--format`);
    }

    if (pluginConfig.prettier) {
      command.push(`--prettier="${pluginConfig.prettier}"`);
    }

    const concatenatedCommand = command.join(' ');

    console.info(
      `Laravel Magic Enums: Running php artisan command: ${concatenatedCommand}`,
    );

    artisan(concatenatedCommand);
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
