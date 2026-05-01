<?php

/**
 * Namespaces Configuration
 * https://www.mediawiki.org/wiki/Manual:$wgNamespaceProtection
 *
 * PHP version 8.3
 *
 * @category Configuration
 * @package  FOSS-Wiki
 * @author   Zoe (atmois) <info@atmois.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link     https://foss.wiki
 */

// https://www.mediawiki.org/wiki/Manual:$wgAvailableRights
$wgAvailableRights[] = 'template-editing';
$wgAvailableRights[] = 'module-editing';
$wgAvailableRights[] = 'meta-editing';

$wgNamespaceProtection[10] = ['template-editing']; // Template:
$wgNamespaceProtection[828] = ['module-editing']; // Module:
$wgNamespaceProtection[4] = ['meta-editing']; // FW:

define("NS_GUIDES", 3000);
define("NS_GUIDES_TALK", 3001);
$wgExtraNamespaces[NS_GUIDES] = "Guides";
$wgExtraNamespaces[NS_GUIDES_TALK] = "Guides_talk";
