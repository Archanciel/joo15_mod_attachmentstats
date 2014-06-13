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
	
	private $countQuery;
	private $lengthAndSizeQuery;
	
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
    	
    	$db->setQuery($this->countQuery);
    	$assoc = $db->loadAssoc();
    	
    	if( $db->getErrorNum () ) {
			$e = $db->getErrorMsg();
			//print_r( $e );
			JError::raiseError( 500, $e );
			return;
    	}
    	
    	$isDataCollected = TRUE;
    	
    	if ($assoc) {
    		$recordingNb = number_format($assoc["R_COUNT"],0,',',' ');
    		$downloadCount = number_format($assoc["DL_COUNT"],0,',',' ');
    	} else {
    		$isDataCollected = FALSE;
    	}
    	
    	$db->setQuery($this->lengthAndSizeQuery);
    	$assoc = $db->loadAssoc();
    	
    	if( $db->getErrorNum () ) {
			$e = $db->getErrorMsg();
			//print_r( $e );
			JError::raiseError( 500, $e );
			return;
    	}
    	
    	if ($assoc) {
    		$totalSize = number_format($assoc["TOT_SIZE"],0,',',' ').' MB';
    		$timeFloat = $assoc["TOT_TIME"];
    		$timeHH = (int)$timeFloat;
    		$timeMM = (int)(($timeFloat - $timeHH) * 60);
    		$timeStr = $timeHH . ' H ' . $timeMM . "'";
    	} else {
    		$isDataCollected = FALSE;
    	}
    	
    	if ($isDataCollected) {
    		return array(REC_COUNT_POS => $recordingNb,REC_TOTAL_SIZE_POS => $totalSize,LISTENING_TOTAL_TIME_POS => $timeStr,DOWNLOAD_COUNT_POS => $downloadCount);
       	} else {
       		return array(REC_COUNT_POS => 0,REC_TOTAL_SIZE_POS => 0,DOWNLOAD_COUNT_POS => 0,LISTENING_TOTAL_TIME_POS => 0);
       	}
     }
	
     /**
      * user_field_2 == ignore_count:	if value is 1, means do not include this line for computing the 
      * 								total number of recordings.
      * user_field_3 == ignore_length:	if value is 1, means do not include this line for computing the
      * 								total recording length and size.
      * 
      * Example:
      * 
      * 													ignore_count	ignore_length
      * 
      * Malvoyant, et exceptionnel: partial attachment 1		empty			empty
      * 							partial attachment 2		  1				empty
      * 
      * Le pouvoir du moment prés:	full attachment				empty			empty
      * 							partial attachment 1		  1				  1
      * 							partial attachment n		  1				  1
      */
     private function __construct() {
     	$this->countQuery = "SELECT COUNT(filename) AS R_COUNT, SUM(download_count) AS DL_COUNT   
							 FROM #__attachments AS a 
     						 WHERE a.user_field_2 = '';";
     	$this->lengthAndSizeQuery = "SELECT SUM(user_field_1) / 60 AS TOT_TIME, SUM(file_size) / 1000000 AS TOT_SIZE 
							 		FROM #__attachments AS a 
     						 		WHERE a.user_field_3 = '';";
     }
}
?>
