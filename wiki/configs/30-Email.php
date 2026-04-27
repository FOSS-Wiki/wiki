<?php

/**
 * Email System Configuration
 *
 * PHP version 8.3
 *
 * @category Configuration
 * @package  FOSS-Wiki
 * @author   Zoe (atmois) <zoe@foss.wiki>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @link     https://foss.wiki
 */

// https://www.mediawiki.org/wiki/Manual:$wgEnableEmail
$wgEnableEmail = true;

// https://www.mediawiki.org/wiki/Manual:$wgEnableUserEmail
$wgEnableUserEmail = true;

// https://www.mediawiki.org/wiki/Manual:$wgEmergencyContact
$wgEmergencyContact = $_ENV['EMERGENCY_EMAIL'];

// https://www.mediawiki.org/wiki/Manual:$wgPasswordSender
$wgPasswordSender = $_ENV['SMTP_USERNAME'];

// https://www.mediawiki.org/wiki/Manual:$wgEmailAuthentication
$wgEmailAuthentication = true;

// https://www.mediawiki.org/wiki/Manual:$wgEmailConfirmToEdit
$wgEmailConfirmToEdit = false;
