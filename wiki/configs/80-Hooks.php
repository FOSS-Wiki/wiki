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
$wgHooks["SkinAddFooterLinks"][] = function ($sk, $key, &$footerlinks) {
    if ($key !== "places") {
        return;
    }

    // Remove existing footers
    unset($footerlinks["about"], $footerlinks["privacy"], $footerlinks["disclaimers"]);

    $rel = "nofollow noreferrer noopener";

    $links = [
        // Column 1
        "about"        => ["footer-about",        "FW:About"],
        "guidelines"   => ["footer-guidelines",   "FW:Guidelines"],
        "disclaimers"  => ["footer-disclaimer",   "FW:Disclaimer"],
        // Column 2
        "statuspage"   => ["footer-statuspage",   "https://status.foss.wiki"],
        "transparency" => ["footer-transparency", "https://open.foss.wiki"],
        "privacy"      => ["footer-privacy",      "FW:Privacy_Policy"],
        // Column 3
        "discord"      => ["footer-discord",      "https://discord.foss.wiki"],
        "donate"       => ["footer-donate",       "https://donate.foss.wiki"],
        "github"       => ["footer-github",       "https://github.foss.wiki"],
    ];

    foreach ($links as $id => [$msg, $target]) {
        $external = str_starts_with($target, "https://");
        $attrs = $external
            ? ["href" => $target, "rel" => $rel]
            : ["href" => Title::newFromText($target)->getFullURL()];

        $footerlinks[$id] = Html::rawElement("a", $attrs, $sk->msg($msg)->escaped());
    }
};
