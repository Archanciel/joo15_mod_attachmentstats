<?php
/**
 * Helper class for Attachment Stats Module
 * 
 * @link http://plusconscient.net
 * @license        GNU/GPL, see LICENSE.php
 * mod_attachment_stats is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
require_once (MOD_ATTACHMENTSTATS_BASE . DS . 'constants.php');

class modAttachmentStatsHelper {
	private static $instance;
	
	private $statsQuery;
	
	public static function getInstance() {
		if (!isset(self::$instance)) {
			$clazz = __CLASS__;
			self::$instance = new $clazz;
		}
		
		return self::$instance;
	}
	
    /**
     * Retrieves the attachment stats.
     *
     * @param array $params An object containing the module parameters
     */    
    public function getStats( $params ) {
    	/* @var $db JDatabase */
    	$db = JFactory::getDBO();
    	
    	$db->setQuery($this->statsQuery);
    	$assoc = $db->loadAssoc();
    	
    	if( $db->getErrorNum () ) {
			$e = $db->getErrorMsg();
			//print_r( $e );
			JError::raiseError( 500, $e );
			return;
    	}
    	
    	if ($assoc) {
    		$timeFloat = $assoc["TOT_TIME"];
    		$timeHH = (int)$timeFloat;
    		$timeMM = (int)(($timeFloat - $timeHH) * 60);
    		$timeStr = $timeHH . ' H ' . $timeMM . "'";
        	return array(REC_COUNT_POS => number_format($assoc["R_COUNT"],0,',',' '),REC_TOTAL_SIZE_POS => number_format($assoc["TOT_SIZE"],0,',',' ').' MB',LISTENING_TOTAL_TIME_POS => $timeStr,DOWNLOAD_COUNT_POS => number_format($assoc["DL_COUNT"],0,',',' '));
    	} else {
        	return array(REC_COUNT_POS => 0,REC_TOTAL_SIZE_POS => 0,DOWNLOAD_COUNT_POS => 0,LISTENING_TOTAL_TIME_POS => 0);
    	}
     }
	
     private function __construct() {
     	$this->statsQuery = "SELECT COUNT(filename) AS R_COUNT".
		    			 ", SUM(user_field_1) / 60 AS TOT_TIME".
		    			 ", SUM(download_count) AS DL_COUNT".
		    			 ", SUM(file_size) / 1000000 AS TOT_SIZE".
		    			 " FROM #__attachments;";
     }
}
?>
