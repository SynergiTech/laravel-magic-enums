import { it, expect } from 'vitest';
import { laravelMagicEnums } from '@/viteLaravelMagicEnums';

it('should return the plugin name as laravel-magic-enums', () => {
  const plugin = laravelMagicEnums({
    input: 'test',
    output: 'test',
  });

  expect(plugin.name).to.be.eql('laravel-magic-enums');
});
