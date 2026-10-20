<?php

/**
 * The root setting for all things Mediawiki
 *
 * PHP version 8.3
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
$configFiles = glob('/var/www/wiki/configs/*.php');
sort($configFiles);
foreach ($configFiles as $configFile) {
    include_once $configFile;
}

// Include run-time overrides
foreach (glob('/var/www/wiki/overrides/*.php') ?: [] as $file) {
    try {
        include $file;
    } catch (\Throwable $e) {
        error_log(basename($file) . ' skipped: ' . $e->getMessage());
    }
}
