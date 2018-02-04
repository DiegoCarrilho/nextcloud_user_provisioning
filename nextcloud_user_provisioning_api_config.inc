<?php 
/**
 * Config file for the class nextcloud_user_provisioning_api
 */


/**
 * An existing nextcloud admin account's user name
 * Will be read from nextcloud_user_provisioning_api_config.inc
 * @var string
 */
$admin_username='';

/**
 * The (existing) nextcloud admin account's password
 * Will be read from nextcloud_user_provisioning_api_config.inc
 * @var string
 */
$admin_password = '';

/**
 * The URL of the nextcloud installation (e. g. "https://www.mynextcloud.tld")
 * Will be read from nextcloud_user_provisioning_api_config.inc
 * @var string
 */
$base_url = '';

/**
 * Debug-Level. If set on true you will get lots of debug information from every function. Can be true or false (default)
 * @var boolean
 */
$debug = false;
?>