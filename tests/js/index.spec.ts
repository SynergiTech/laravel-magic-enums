import { artisan } from '@/utils.js';

import { describe, it, expect, beforeEach, vi } from 'vitest';

describe('index.js', async () => {
  async function importEnums() {
    const { enums } = await import(
      // @ts-expect-error This is generated
      '../../workbench/resources/js/laravel-magic-enums/index.js'
    );

    return enums;
  }

  beforeEach(() => {
    vi.resetModules();
  });

  it('matches the expected structure', async () => {
    artisan(
      'laravel-magic-enums:generate --output=workbench/resources/js/laravel-magic-enums',
    );

    const enums = await importEnums();

    expect(enums).toEqual({
      EnumWithSpaces: {
        TwentyOne: { name: 'TwentyOne', value: 'Twenty One' },
        TwentyTwo: { name: 'TwentyTwo', value: 'Twenty Two' },
      },
      CustomEnum: {
        Alpha: {
          name: 'Alpha',
          value: 'alpha',
          add_three: 'delta',
          'something else': 'alpha',
        },
        Beta: {
          name: 'Beta',
          value: 'beta',
          add_three: 'echo',
          'something else': 'beta',
        },
        Charlie: {
          name: 'Charlie',
          value: 'charlie',
          add_three: 'foxtrot',
          'something else': 'charlie',
        },
      },
      TestingEnum: {
        First: { name: 'First', value: 'first', colours: 'purple' },
        Second: { name: 'Second', value: 'second', colours: 'yellow' },
        Third: { name: 'Third', value: 'third', colours: 'green' },
        Fourth: { name: 'Fourth', value: 'fourth', colours: null },
        Fifth: { name: 'Fifth', value: 'fifth', colours: null },
        Sixth: { name: 'Sixth', value: 'sixth', colours: null },
        Seventh: { name: 'Seventh', value: 'seventh', colours: null },
        Eighth: { name: 'Eighth', value: 'eighth', colours: null },
      },
      TestingEnumThreeQuarters: {
        First: { name: 'First', value: 'first', colours: 'purple' },
        Second: { name: 'Second', value: 'second', colours: 'yellow' },
        Third: { name: 'Third', value: 'third', colours: 'green' },
        Fourth: { name: 'Fourth', value: 'fourth', colours: null },
        Fifth: { name: 'Fifth', value: 'fifth', colours: null },
        Sixth: { name: 'Sixth', value: 'sixth', colours: null },
      },
    });
  });

  it('resolves enum values with spaces to their name', async () => {
    artisan(
      'laravel-magic-enums:generate --output=workbench/resources/js/laravel-magic-enums',
    );

    const enums = await importEnums();

    expect(enums['EnumWithSpaces']['Twenty One']).toEqual({
      name: 'TwentyOne',
      value: 'Twenty One',
    });

    expect(enums['EnumWithSpaces']['TwentyOne']).toEqual({
      name: 'TwentyOne',
      value: 'Twenty One',
    });
  });

  it('the exported enums are frozen', async () => {
    artisan(
      'laravel-magic-enums:generate --output=workbench/resources/js/laravel-magic-enums',
    );

    const enums = await importEnums();

    expect(Object.isFrozen(enums)).toBe(true);
  });
});
