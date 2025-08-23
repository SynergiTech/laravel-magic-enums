const enums: Record<string, unknown> = {};

export function setEnums(options: { [x: string]: never }) {
  for (const key in options) {
    enums[key] = new Proxy(options[key], {
      get(target, prop) {
        if (typeof prop !== 'string') {
          return false;
        }

        const normalisedKey = prop.replaceAll(' ', '');

        if (Reflect.has(target, normalisedKey)) {
          return Reflect.get(target, normalisedKey);
        }

        return false;
      },
    });
  }

  // Prevent mutations.
  Object.freeze(enums);
}

export async function vueEnumPlugin(path: string) {
  return {
    async install() {
      setEnums(await import(path));
    },
  };
}

export function useEnums() {
  return enums;
}
