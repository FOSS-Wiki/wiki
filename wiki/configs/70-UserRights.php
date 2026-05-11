<?php

/**
 * Mediawiki Permissions Configuration
 * https://www.mediawiki.org/wiki/Manual:User_rights
 * checkuser, supress, interface-admin & push-subscription-manager are dead
 * groups are in order of increasing rank
 *
 * PHP version 8.3
 *
 * @category Configuration
 * @package  FOSS-Wiki
 * @author   Zoe (atmois) <info@atmois.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link     https://foss.wiki
 */

// * ~ Everyone
$wgGroupPermissions['*']['createaccount'] = true;
$wgGroupPermissions['*']['edit'] = false;

// user ~ Registered Users
$wgGroupPermissions['user']['edit'] = true;
$wgGroupPermissions['user']['writeapi'] = true; // For visual editor
$wgGroupPermissions['user']['move'] = false;
$wgGroupPermissions['user']['move-subpages'] = false;
$wgGroupPermissions['user']['move-categorypages'] = false;
$wgGroupPermissions['user']['move-rootuserpages'] = false;
$wgGroupPermissions['user']['movefile'] = false;

// autoconfirmed ~ Wiki Editor Role
$wgAutoConfirmAge = 259200;
$wgAutoConfirmCount = 10;
$wgGroupPermissions['autoconfirmed']['autoconfirmed'] = false;
$wgGroupPermissions['autoconfirmed']['editsemiprotected'] = false;

// template-editor ~ Template and Module Editors
$wgGroupPermissions['template-editor']['template-editing'] = true;
$wgGroupPermissions['template-editor']['module-editing'] = true;

// wiki-team ~ FOSS Wiki Staff
$wgGroupPermissions['wiki-team']['template-editing'] = true;
$wgGroupPermissions['wiki-team']['module-editing'] = true;
$wgGroupPermissions['wiki-team']['meta-editing'] = true;
$wgGroupPermissions['wiki-team']['move'] = true;
$wgGroupPermissions['wiki-team']['move-subpages'] = true;
$wgGroupPermissions['wiki-team']['move-categorypages'] = true;
$wgGroupPermissions['wiki-team']['move-rootuserpages'] = true;
$wgGroupPermissions['wiki-team']['editsemiprotected'] = true;
$wgGroupPermissions['wiki-team']['movefile'] = true;
$wgGroupPermissions['wiki-team']['block'] = true;
$wgGroupPermissions['wiki-team']['rollback'] = true;

$wgAddGroups['wiki-team'] = array('template-editor', 'autoconfirmed');
$wgRemoveGroups['wiki-team'] = array('template-editor', 'autoconfirmed', "wiki-team");

// sysop ~ FOSS Wiki Leadership
$wgGroupPermissions['sysop']['checkuser'] = true;
$wgGroupPermissions['sysop']['checkuser-log'] = true;
$wgGroupPermissions['sysop']['investigate'] = true;
$wgGroupPermissions['sysop']['userrights'] = true;
$wgGroupPermissions['sysop']['userrights-interwiki'] = true;
$wgGroupPermissions['sysop']['editusercss'] = true;
$wgGroupPermissions['sysop']['edituserjson'] = true;
$wgGroupPermissions['sysop']['edituserjs'] = true;
$wgGroupPermissions['sysop']['editsitecss'] = true;
$wgGroupPermissions['sysop']['editsitejson'] = true;
$wgGroupPermissions['sysop']['editsitejs'] = true;
$wgGroupPermissions['sysop']['editinterface'] = true;
$wgGroupPermissions['sysop']['template-editing'] = true;
$wgGroupPermissions['sysop']['module-editing'] = true;
$wgGroupPermissions['sysop']['meta-editing'] = true;
$wgGroupPermissions['sysop']['interwiki'] = true;
$wgGroupPermissions['sysop']['block'] = true;
$wgGroupPermissions['sysop']['rollback'] = true;
$wgGroupPermissions['sysop']['renameuser'] = true;
$wgGroupPermissions['sysop']['suppressrevision'] = true;
$wgGroupPermissions['sysop']['hideuser'] = true;
$wgGroupPermissions['sysop']['deletelogentry'] = true;
$wgGroupPermissions['sysop']['deleterevision'] = true;
$wgGroupPermissions['sysop']['suppressionlog'] = true;
$wgGroupPermissions['sysop']['viewsuppressed'] = true;
$wgGroupPermissions['sysop']['usermerge'] = true;
$wgGroupPermissions['sysop']['mwoauthproposeconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthupdateownconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthmanageconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthsuppress'] = true;
$wgGroupPermissions['sysop']['mwoauthviewsuppressed'] = true;
$wgGroupPermissions['sysop']['mwoauthviewprivate'] = true;
$wgGroupPermissions['sysop']['mwoauthmanagemygrants'] = true;
$wgGroupPermissions['sysop']['import'] = false;
