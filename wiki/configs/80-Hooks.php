<?php

/**
 * The Mediawiki Hooks Configuration
 *
 * PHP version 8.5
 *
 * @category Configuration
 * @package  FOSS-Wiki
 * @author   atmois <info@atmois.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link     https://foss.wiki
 */

// For the footer stuff
use MediaWiki\Html\Html;
use MediaWiki\Title\Title;

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

// Remove unused groups: 'suppress', 'checkuser', 'push-subscription-manager', 'temporary-account-viewer'
$wgHooks['MediaWikiServices'][] = static function () {
    global $wgGroupPermissions, $wgRevokePermissions, $wgAddGroups,
           $wgRemoveGroups, $wgGroupsAddToSelf, $wgGroupsRemoveFromSelf;

    $unusedGroups = [
        'suppress',
        'checkuser',
        'push-subscription-manager',
        'temporary-account-viewer'
    ];

    foreach ($unusedGroups as $group) {
        unset($wgGroupPermissions[$group]);
        unset($wgRevokePermissions[$group]);
        unset($wgAddGroups[$group]);
        unset($wgRemoveGroups[$group]);
        unset($wgGroupsAddToSelf[$group]);
        unset($wgGroupsRemoveFromSelf[$group]);
    }
};

// Add custom footer links
$wgHooks['SkinAddFooterLinks'][] = function (Skin $skin, string $key, array &$footerlinks) {
    if ($key !== 'places') {
        return;
    }

    $rel = 'nofollow noreferrer noopener';

    $link = static function ($msg, $target) use ($skin, $rel) {
        $attrs = str_starts_with($target, 'https://')
            ? ['href' => $target, 'rel' => $rel]
            : ['href' => Title::newFromText($target)->getFullURL()];

        return Html::rawElement('a', $attrs, $skin->msg($msg)->escaped());
    };

    // Column 1
    $footerlinks['about']        = $link('footer-about', 'FW:About');
    $footerlinks['guidelines']   = $link('footer-guidelines', 'FW:Guidelines');
    $footerlinks['transparency'] = $link('footer-transparency', 'https://open.foss.wiki');

    // Column 2
    $footerlinks['statuspage']   = $link('footer-statuspage', 'https://status.foss.wiki');
    $footerlinks['privacy']      = $link('footer-privacy', 'FW:Privacy_Policy');
    $footerlinks['disclaimers']  = $link('footer-disclaimer', 'FW:Disclaimer');
    // Column 3
    $footerlinks['discord']      = $link('footer-discord', 'https://discord.foss.wiki');
    $footerlinks['donate']       = $link('footer-donate', 'https://donate.foss.wiki');
    $footerlinks['github']       = $link('footer-github', 'https://github.foss.wiki');
};
