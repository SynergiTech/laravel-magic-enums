# Upgrading from v2 to v3

1. Remove the vue plugin from `app.js`
2. Drop `MagicEnumsRouteFacade::enumsController()` from routes.
3. Change the vite plugin usage to `laravelMagicEnums()` or `laravelMagicEnums({ input: 'your-enum-folder' })` if you had it customised
4. Change frontend usage:

```js
// Before:
import { useEnums } from 'laravel-magic-enums';

const { YourEnum } = useEnums();

// After:
import { enums } from 'resources/js/magic-enums';

const { YourEnum } = enums;
```

5. Before building your project, you should run `php artisan laravel-magic-enums:generate` to generate a fresh export of enums.
