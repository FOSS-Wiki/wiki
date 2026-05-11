<?php

/**
 * The Mediawiki Hooks Configuration
 *
 * PHP version 8.3
 *
 * @category Configuration
 * @package  FOSS-Wiki
 * @author   Zoe (atmois) <info@atmois.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link     https://foss.wiki
 */

// Redirect /index.php?title=Page to /Page
$wgHooks['SkinTemplateNavigation::Universal'][] = function ($skin, &$links) {
    foreach ($links as &$group) {
        foreach ($group as &$tab) {
            if (isset($tab['href'])) {
                $tab['href'] = preg_replace_callback(
                    '#/index\.php\?title=([^&]+)(&(.*))?#',
                    function ($matches) {
                        $title = $matches[1];
                        $query = isset($matches[3]) ? '?' . $matches[3] : '';
                        return '/' . $title . $query;
                    },
                    $tab['href']
                );
            }
        }
    }
    return true;
};

// Remove unused groups: 'suppress', 'checkuser', 'push-subscription-manager', 'temporary-account-viewer
$wgHooks['MediaWikiServices'][] = static function () {
    global $wgGroupPermissions, $wgRevokePermissions, $wgAddGroups,
           $wgRemoveGroups, $wgGroupsAddToSelf, $wgGroupsRemoveFromSelf;

    foreach (['suppress', 'checkuser', 'push-subscription-manager', 'temporary-account-viewer'] as $group) {
        unset($wgGroupPermissions[$group]);
        unset($wgRevokePermissions[$group]);
        unset($wgAddGroups[$group]);
        unset($wgRemoveGroups[$group]);
        unset($wgGroupsAddToSelf[$group]);
        unset($wgGroupsRemoveFromSelf[$group]);
    }
};
