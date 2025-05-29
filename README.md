# Laravel Magic Enums

Have you ever wanted to reference your PHP enums in your frontend code but ended up (or didn't want to end up) duplicating them manually? Well here is your answer.

## Installing

You need both sides to get started.

```sh
$ composer require synergitech/laravel-magic-enums
$ npm install --save laravel-magic-enums
```

## Getting Started

1. Add the trait from this package to your enums

```php
<?php

namespace App\Enums;

use SynergiTech\MagicEnums\Traits\WithToVueArray;

enum YourEnum: string
{
    use WithToVueArray;
...
```

2. Include the route somewhere in your routes file of choice, in this example we are going to create `/api/enums`.

```php
<?php

use Illuminate\Support\Facades\Route;
use SynergiTech\MagicEnums\Facades\MagicEnumsRouteFacade;

Route::prefix('/api')->group(function () {
    MagicEnumsRouteFacade::enumsController();
});
```

You can obviously include middleware on it if you wish, i.e. for an authenticated session, but this may affect your frontends ability to initialise so please be careful.

We recommend you don't include sensitive information in your enums.

3. We work primarily with Inertia and Vue so the integration looks something like this (noting the async/ await and re use of the route we created earlier)

```js
import { vueEnumPlugin } from "laravel-magic-enums";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob("./Pages/**/*.vue");
    return pages[`./Pages/${name}.vue`]();
  },

  async setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(await vueEnumPlugin("/api/enums"))
      .mount(el);
  },
...
```

4. During development you probably need the page to reload if files change in your enums directory and you may even be interested in types for your typescript so you can update your vite.config.js as follows

You'll notice we provide both the directory and the endpoint.

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
      enumDir: "./app/Enums",
      enumEndpoint: "http://localhost/api/enums",
      interfaceOutput: "./resources/js/globals.d.ts",
    }),
  ],
...
```

5. Now in your components you can reference your enums as if they were key value objects

```js
import { useEnums } from 'laravel-magic-enums';

const { YourEnum, YourOtherEnum } = useEnums();
```

## Advanced Usage

You may choose to have an array within your enum of a subset of the values for a specific purpose or grouping.

If you use the PHP attribute `SynergiTech\MagicEnums\Attributes\AppendConstToMagic` then an extra enum representing this will be available in the components.

You may also have an array which maps some or all of the values of the enum to a different string.

If you use the PHP attribute `SynergiTech\MagicEnums\Attributes\AppendValueToMagic` then an extra enum representing this will be available in the components.

For example

```php
<?php

namespace App\Enums;

use SynergiTech\MagicEnums\Attributes\AppendConstToMagic;
use SynergiTech\MagicEnums\Attributes\AppendValueToMagic;
use SynergiTech\MagicEnums\Traits\WithToVueArray;

enum TestingEnum: string
{
    use WithToVueArray;

    case First = 'first';
    case Second = 'second';
    case Third = 'third';

    #[AppendConstToMagic]
    public const JUST_ONE = [
        self::First,
    ];

    #[AppendValueToMagic]
    public const COLOURS = [
        self::First->value => 'red',
    ];
}
```

will create the output

```js
TestingEnum: {
  First: "first",
  Second: "second",
  Third: "third"
},
TestingEnumJustOne: {
  First: "first"
},
TestingEnumColours: {
  First: "red"
}
```
