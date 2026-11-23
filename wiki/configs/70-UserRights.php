<?php

/**
 * Mediawiki Permissions Configuration
 * https://www.mediawiki.org/wiki/Manual:User_rights
 * checkuser, supress, interface-admin & push-subscription-manager are dead
 * groups are in order of increasing rank
 *
 * PHP version 8.5
 *
 * @category Configuration
 * @package  FOSS-Wiki
 * @author   atmois <info@atmois.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link     https://foss.wiki
 */

// https://www.mediawiki.org/wiki/Manual:$wgAutoCreateTempUser
// Enable temp accounts with 30 day lifespan and warning after 14 days
$wgAutoCreateTempUser['enabled'] = true;
$wgAutoCreateTempUser['expireAfterDays'] = 28;
$wgAutoCreateTempUser['notifyBeforeExpirationDays'] = 14;

// https://www.mediawiki.org/wiki/Manual:$wgPrivilegedGroups
$wgPrivilegedGroups = ['bureaucrat', 'interface-admin', 'sysop', 'steward'];

// everyone
$wgGroupPermissions['*']['createpage'] = false;
$wgGroupPermissions['*']['createtalk'] = false;

// user
$wgGroupPermissions['user']['createpage'] = true;
$wgGroupPermissions['user']['createtalk'] = true;
$wgGroupPermissions['user']['move'] = false;
$wgGroupPermissions['user']['move-subpages'] = false;
$wgGroupPermissions['user']['move-categorypages'] = false;
$wgGroupPermissions['user']['move-rootuserpages'] = false;
$wgGroupPermissions['user']['movefile'] = false;

// autoconfirmed
$wgAutoConfirmAge = 259200;
$wgAutoConfirmCount = 10;
$wgGroupPermissions['autoconfirmed']['autopatrol'] = true;
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;

// template-editor
$wgGroupPermissions['template-editor']['template-editing'] = true;
$wgGroupPermissions['template-editor']['module-editing'] = true;

// steward
$wgGroupPermissions['steward']['autopatrol'] = true;
$wgGroupPermissions['steward']['template-editing'] = true;
$wgGroupPermissions['steward']['module-editing'] = true;
$wgGroupPermissions['steward']['meta-editing'] = true;
$wgGroupPermissions['steward']['move'] = true;
$wgGroupPermissions['steward']['move-subpages'] = true;
$wgGroupPermissions['steward']['move-categorypages'] = true;
$wgGroupPermissions['steward']['move-rootuserpages'] = true;
$wgGroupPermissions['steward']['editsemiprotected'] = true;
$wgGroupPermissions['steward']['movefile'] = true;
$wgGroupPermissions['steward']['block'] = true;
$wgGroupPermissions['steward']['rollback'] = true;
$wgGroupPermissions['steward']['editalluserpages'] = true;
$wgAddGroups['steward'] = array('template-editor');
$wgRemoveGroups['steward'] = array('template-editor', 'steward');

// sysop
$wgGroupPermissions['sysop']['checkuser'] = true;
$wgGroupPermissions['sysop']['checkuser-log'] = true;
$wgGroupPermissions['sysop']['investigate'] = true;
$wgGroupPermissions['sysop']['template-editing'] = true;
$wgGroupPermissions['sysop']['module-editing'] = true;
$wgGroupPermissions['sysop']['meta-editing'] = true;
$wgGroupPermissions['sysop']['interwiki'] = true;
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
$wgGroupPermissions['sysop']['editalluserpages'] = true;
$wgGroupPermissions['sysop']['import'] = false;
$wgAddGroups['sysop'] = array('template-editor', 'steward');
$wgRemoveGroups['sysop'] = array('template-editor', 'steward', 'sysop');
$wgRestrictedGroups['sysop'] = ['memberConditions' => [ 'oath.has_2fa' ]];

// interface-admin
$wgGroupPermissions['interface-admin']['template-editing'] = true;
$wgGroupPermissions['interface-admin']['module-editing'] = true;
$wgGroupPermissions['interface-admin']['meta-editing'] = true;
$wgRestrictedGroups['interface-admin'] = ['memberConditions' => [ 'oath.has_2fa' ]];

// bureaucrat
$wgRestrictedGroups['bureaucrat'] = ['memberConditions' => [ 'oath.has_2fa' ]];
