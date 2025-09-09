[![JS Tests](https://github.com/SynergiTech/laravel-magic-enums/actions/workflows/js-test.yaml/badge.svg?branch=main)](https://github.com/SynergiTech/laravel-magic-enums/actions/workflows/js-test.yaml)
[![PHP Tests](https://github.com/SynergiTech/laravel-magic-enums/actions/workflows/php-test.yaml/badge.svg)](https://github.com/SynergiTech/laravel-magic-enums/actions/workflows/php-test.yaml)

# Laravel Magic Enums

Have you ever wanted to reference your PHP enums in your frontend code but ended up (or didn't want to end up) duplicating them manually? Well here is your answer.

## Installing

You need both sides to get started.

```sh
$ composer require synergitech/laravel-magic-enums
$ npm install --save laravel-magic-enums
```

## Getting Started

1. Add the trait and interface from this package to your enums. We recommend you don't include sensitive information in your enums.

```php
<?php

namespace App\Enums;

use SynergiTech\MagicEnums\Interfaces\MagicEnum;
use SynergiTech\MagicEnums\Traits\HasMagic;

enum YourEnum: string implements MagicEnum
{
    use HasMagic;
...
```

2. Generate an export of enums with `php artisan laravel-magic-enums:generate`. This will create a file at `resources/js/magic-enums/index.js`.

3. Use the exported enums in your frontend like so. Your IDE will any types from your enums:

```js
import { enums } from 'resources/js/magic-enums/index.js';

const { TestingEnums } = enums;
```

4. During development, you can have Vite react automatically to changes in your enums. The `laravel-magic-enums/vite` plugin provides a simple way to do this. Under the hood, this calls the `php artisan laravel-magic-enums:generate` command, and can customise it. For example, here's how to use it with some customisations:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { laravelMagicEnums } from "laravel-magic-enums/vite";

export default defineConfig({
  plugins: [
    laravel({
        ...
    }),
    vue({
        ...
    }),
    laravelMagicEnums({
      input: 'app/Enums',
      output: 'resources/js/my-enums',
      format: true,
    }),
  ],
...
```

5. We recommend adding the exported enums to the `.gitignore` file.

## Advanced Usage

### Sub Enums

You may choose to have an array within your enum of a subset of the values for a specific purpose or grouping.

If you use the PHP attribute `SynergiTech\MagicEnums\Attributes\AppendConstToMagic`, then an extra enum representing this will be available in the frontend.

You may also have an array which maps some or all of the values of the enum to a different string.

If you use the PHP attribute `SynergiTech\MagicEnums\Attributes\AppendValueToMagic`, then an extra enum representing this will be available in the frontend.

For example:

```php
<?php

namespace App\Enums;

use SynergiTech\MagicEnums\Attributes\AppendConstToMagic;
use SynergiTech\MagicEnums\Attributes\AppendValueToMagic;
use SynergiTech\MagicEnums\Interfaces\MagicEnum;
use SynergiTech\MagicEnums\Traits\HasMagic;

enum TestingEnum: string implements MagicEnum
{
    use HasMagic;

    case First = 'first';
    case Second = 'second';
    case Third = 'third';

    #[AppendConstToMagic]
    public const JUST_ONE = [
        self::First,
    ];

    #[AppendValueToMagic]
    public const COLOUR = [
        self::First->value => 'red',
    ];
}
```

Will create the output:

```js
TestingEnum: {
  First: {
    "name": "First",
    "value": "first",
    "colour": "red"
  },
  Second: {
    "name": "Second",
    "value": "second",
    "colour": null
  },
  Third: {
    "name": "Third",
    "value": "third",
    "colour": null
  }
},
TestingEnumJustOne: {
  First: {
    "name": "First",
    "value": "first",
    "colour": "red"
  }
}
```

### Extending

If you wish to have more control over appending values to your magic enums for the frontend, you can extend the current trait using something along the lines of [app/Traits/CustomMagic.php](app/Traits/CustomMagic.php) as long as you always follow the interface and provide the function.
