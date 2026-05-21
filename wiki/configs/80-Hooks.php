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

// Remove unused groups: 'suppress', 'checkuser', 'push-subscription-manager', 'temporary-account-viewer', 'bureaucrat'
$wgHooks['MediaWikiServices'][] = static function () {
    global $wgGroupPermissions, $wgRevokePermissions, $wgAddGroups,
           $wgRemoveGroups, $wgGroupsAddToSelf, $wgGroupsRemoveFromSelf;

    $unusedGroups = [
        'suppress',
        'checkuser',
        'push-subscription-manager',
        'temporary-account-viewer',
        'bureaucrat',
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
    $rel = "nofollow noreferrer noopener";

    $footerlinks["about"] = Html::rawElement(
        "a",
        [
            "href" => Title::newFromText("FW:About")->getFullURL(),
        ],
        $sk->msg("footer-about")->escaped(),
    );
    $footerlinks["guidelines"] = Html::rawElement(
        "a",
        [
            "href" => Title::newFromText("FW:Guidelines")->getFullURL(),
        ],
        $sk->msg("footer-guidelines")->escaped(),
    );
    $footerlinks["disclaimer"] = Html::rawElement(
        "a",
        [
            "href" => Title::newFromText("FW:Disclaimer")->getFullURL(),
        ],
        $sk->msg("footer-disclaimer")->escaped(),
    );
    $footerlinks["statuspage"] = Html::rawElement(
        "a",
        [
            "href" => "https://status.foss.wiki",
            "rel" => $rel,
        ],
        $sk->msg("footer-statuspage")->escaped(),
    );
    $footerlinks["transparency"] = Html::rawElement(
        "a",
        [
            "href" => "https://open.foss.wiki",
            "rel" => $rel,
        ],
        $sk->msg("footer-transparency")->escaped(),
    );
    $footerlinks["privacy"] = Html::rawElement(
        "a",
        [
            "href" => Title::newFromText("FW:Privacy_policy")->getFullURL(),
        ],
        $sk->msg("footer-privacy")->escaped(),
    );
    $footerlinks["discord"] = Html::rawElement(
        "a",
        [
            "href" => "https://discord.foss.wiki",
            "rel" => $rel,
        ],
        $sk->msg("footer-discord")->escaped(),
    );
    $footerlinks["donate"] = Html::rawElement(
        "a",
        [
            "href" => "https://donate.foss.wiki",
            "rel" => $rel,
        ],
        $sk->msg("footer-donate")->escaped(),
    );
    $footerlinks["github"] = Html::rawElement(
        "a",
        [
            "href" => "https://github.foss.wiki",
            "rel" => $rel,
        ],
        $sk->msg("footer-github")->escaped(),
    );
};
