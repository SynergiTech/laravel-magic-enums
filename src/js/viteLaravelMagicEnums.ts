import { promises as fs } from "fs";
import { exec } from "child_process";
import chokidar, { type FSWatcher, type ChokidarOptions } from "chokidar";
import type { Plugin, ResolvedConfig } from "vite";

interface PluginOptions {
  /**
   * The directory to watch for changes.
   * @default app/Enums
   */
  enumDir?: string;
  /**
   * The Laravel endpoint to fetch the enums from.
   * @default //localhost/enums
   */
  enumEndpoint?: string;
  /**
   * The output file for the enum interface.
   * @default magic-enums.d.ts
   */
  interfaceOutput?: string;
  /**
   * Additional options to pass to chokidar.
   */
  chokidarOptions?: ChokidarOptions;
  /**
   * The command to run prettier and format the enum export. A value of `undefined` will not run prettier.
   * @default undefined
   */
  prettierCommand?: string | undefined;
}

const defaultOptions = {
  enumDir: "app/Enums",
  enumEndpoint: "//localhost/enums",
  interfaceOutput: "laravel-magic-enums.d.ts",
  prettierCommand: undefined,
};

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
  let resolvedConfig: ResolvedConfig = {} as ResolvedConfig;

  const pluginConfig = {
    ...defaultOptions,
    ...options,
    chokidarOptions: {
      ...defaultChokidarOptions,
      ...(options.chokidarOptions ?? {}),
    },
  };

  Object.assign(pluginConfig.chokidarOptions, options.chokidarOptions ?? {});

  const listenToEnumFolder = debounce(async function (e: string) {
    if (e.startsWith(pluginConfig.enumDir.slice(2))) {
      await exportEnums();
    }
  }, 200);

  async function exportEnums() {
    const get = async function () {
      const response = await fetch(pluginConfig.enumEndpoint);

      if (!response.ok) {
        throw new Error("Failed to fetch enums");
      }

      const json = await response.json();

      await fs.writeFile(
        pluginConfig.interfaceOutput,
        `
        declare global {
          interface LaravelMagicEnums ${JSON.stringify(json)};
        }
        `
      );

      // Prettier.
      if (pluginConfig.prettierCommand) {
        exec(
          `${pluginConfig.prettierCommand} --write ${pluginConfig.interfaceOutput}`
        );
      }
    };

    console.info("Rebuilding enums file...");

    try {
      await get();
      console.info("... Rebuilt enums file!");
    } catch (e) {
      console.error(
        "Failed to rebuild enums file. Trying again in 2 seconds.",
        e
      );

      setTimeout(exportEnums, 2000);
    }
  }

  return {
    name: "laravel-magic-enums",
    configResolved(config) {
      resolvedConfig = config;

      fsWatcher = chokidar
        .watch(pluginConfig.enumDir, pluginConfig.chokidarOptions)
        .on("change", listenToEnumFolder)
        .on("add", listenToEnumFolder)
        .on("unlink", listenToEnumFolder);

      if (resolvedConfig.mode === "development") {
        exportEnums();
      }
    },

    buildEnd() {
      fsWatcher?.close();
    },
  };
}

// https://decipher.dev/30-seconds-of-typescript/docs/debounce/
function debounce(fn: Function, ms = 300) {
  let timeoutId: ReturnType<typeof setTimeout>;
  return function (this: unknown, ...args: unknown[]) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), ms);
  };
}
