<?php
/**
 * Attachment Stats Module Entry Point
 * 
 * @link http://plusconscient.net
 * @license        GNU/GPL, see LICENSE.php
 * mod_attachment_stats is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('MOD_ATTACHMENTSTATS_BASE')) {
	define( 'MOD_ATTACHMENTSTATS_BASE', dirname(__FILE__) );
}

// Include the syndicate functions only once
require_once( MOD_ATTACHMENTSTATS_BASE.DS.'helper.php' );
 
$stats_array = modAttachmentStatsHelper::getInstance()->getStats( $params, NULL );
require( JModuleHelper::getLayoutPath( 'mod_attachmentstats' ) );
?>
