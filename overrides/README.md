# Runtime Overrides

Any PHP file placed in this directory will be loaded by the wiki after all the main configs, so it will override anything else set in the main configs without the need to restart/rebuild the container.

This is for temporary, runtime-only tweaks, not permanent configuration. Configurations such as loading new extensions or skins, or changing the database, **should not be done here.**

Any `.php` file is loaded, in alphabetical order, I suggest using numbers to control the loading order (`10-readonly.php`, `20-debug.php`). If a file has a syntax error or throws an exception, it is skipped and the wiki keeps running on the base config be aware that this can't catch everything so check the file is valid before saving it.

Changes apply within a few seconds. Cached pages may need a purge or hard refresh before you see the effect. Deleting a file removes its effect on the next request made to the wiki.

Don't use an editor with auto-save in this directory. A half-written file may load partially and behave unexpectedly. If you want to edit a file, do it in a temporary location and then copy it over when ready. You can draft in a file with a `.tmp` extension, then rename it when ready to ensure only the final file is loaded.

# Templates

## Base File

```php
<?php

// Runtime-only tweaks or overrides
```

## Maintenance Mode

Useful for database upgrades or other maintenance tasks. This will display a maintenance message to all users preventing them from editing or logging in. You can set a custom message (including with wikitext) in the configuration.

```php
<?php
// 10-readonly.php
$wgReadOnly = 'Maintenance in progress';
```
