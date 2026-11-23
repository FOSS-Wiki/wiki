<?php

/**
 * The root setting for all things Mediawiki
 *
 * PHP version 8.5
 *
 * @category Configuration
 * @package  FOSS-Wiki
 * @author   atmois <info@atmois.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link     https://foss.wiki
 */

if (!defined('MEDIAWIKI')) {
    exit;
}

// Loads the config files in order
$configFiles = glob('/var/www/wiki/configs/*.php') ?: [];
foreach ($configFiles as $configFile) {
    include_once $configFile;
}

foreach (glob('/var/www/wiki/overrides/*.php') ?: [] as $overrideFile) {
    try {
        include $overrideFile;
    } catch (\Throwable $e) {
        error_log(basename($overrideFile) . ' skipped: ' . $e->getMessage());
    }
}

unset($configFiles, $configFile, $overrideFile, $e);
