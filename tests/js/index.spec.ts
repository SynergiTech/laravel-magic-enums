import { describe, it, expect, beforeEach, vi, afterAll } from 'vitest';
import { promises as fs } from 'node:fs';
import { execSync } from 'node:child_process';
import path from 'node:path';

function artisan(command: string): void {
  const testbenchDir = path.join('vendor', 'bin', 'testbench');
  execSync(`${testbenchDir} ${command}`).toString('utf8');
}

describe('index.js', async () => {
  const outputDir = 'workbench/resources/js/enums';
  let i = 0;

  async function importEnums() {
    i++;
    const { enums } = await import(
      '../../workbench/resources/js/enums/index.js?t=' + i
    );

    return enums;
  }

  beforeEach(() => {
    vi.resetModules();
  });

  afterAll(async () => {
    await fs.rm('workbench/resources/js/enums', {
      recursive: true,
      force: true,
    });
  });

  it('matches the expected structure', async () => {
    artisan(`laravel-magic-enums:generate --output=${outputDir}`);

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
    artisan(`laravel-magic-enums:generate --output=${outputDir}`);

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
    artisan(`laravel-magic-enums:generate --output=${outputDir}`);

    const enums = await importEnums();

    expect(Object.isFrozen(enums)).toBe(true);
  });

  it('when the format argument is passed, the generated file is formatted', async () => {
    artisan(`laravel-magic-enums:generate --output=dummy`);
    const unformatted = await fs.readFile(`dummy/index.js`, 'utf-8');

    artisan(`laravel-magic-enums:generate --output=dummy --format`);
    const formatted = await fs.readFile(`dummy/index.js`, 'utf-8');

    await fs.rm('dummy', { recursive: true, force: true });

    expect(unformatted).not.eq(formatted);
  });
});
