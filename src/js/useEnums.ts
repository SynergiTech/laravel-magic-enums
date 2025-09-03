type MagicEnum = Record<string, unknown>;
const enums: MagicEnum = {};

export function setEnums(options: Record<string, MagicEnum>) {
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

export function useEnums() {
  return enums;
}
