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
jimport('joomla.error.log');

class modAttachmentStatsHelper {
	private static $instance;
	
	private $statsQuery;
	private $dailyStatsBootstrapQuery;
	private $dailyStatsCountQuery;
	private $dailyStatsMaxDateQuery;
	private $dailyStatsShiftDateQuery;
	private $dailyStatsForNewAttachmentsQuery;
	
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
     
     /**
      * This method is called when the following request is made on plusconscient:
      * http://localhost/plusconscient/index.php/a-propos?cron=yes. The request is
      * performed daily by a cron job.
      * 
      * The method inserts daily statitics in the jox_daily_stats table.
      */
     public function execDailyStatsCron() {
    	/* @var $db JDatabase */
    	$db = JFactory::getDBO();
    	$log = JLog::getInstance("mod_attachmentstats_log.php");
		$query = $this->dailyStatsCountQuery;
    	
    	$count = $this->loadResult($db, $query);
    	
    	if ($count > 0) {
    		// daily_stats table not empty
    		$query = $this->dailyStatsMaxDateQuery;
    		$maxDate = $this->loadResult($db, $query);
    		$today = date("Y-m-d");
    		
    		if (strcmp($maxDate,$today) == 0) {
    			// protecting for duplicate insertion of daily stats data
				$entry = array ('LEVEL' => '1', 'STATUS' => 'INFO:', 'COMMENT' => "Stats for today already exist in daily_stats table. No data inserted." );
				$log->addEntry($entry);
    			return;
    		}
    		
    		// inserting daily_stats for neew attachments
    		
    		$query = $this->dailyStatsForNewAttachmentsQuery;
    		$rowsNumberForNewAttachments = $this->executeQuery($db, $query);
    		
    		// inserting daily_stats for existing attachments
    		
    		$gap = 0;	// used to handle the case where cron execution was skipped the day(S) before 
    		$rowsNumberForExistingAttachments = 0;
    		
    		while ( $rowsNumberForExistingAttachments == 0	&&
    				$gap < 20) {
		    	$gap++;
    			$dailyStatsQuery = "INSERT INTO #__daily_stats (article_id, attachment_id, date, total_hits_to_date, date_hits, total_downloads_to_date, date_downloads) ".
									"SELECT T2.id AS article_id, T1.id as attachment_id, CURRENT_DATE, T2.hits, T2.hits - T3.total_hits_to_date, T1.download_count,  T1.download_count - T3.total_downloads_to_date ".
									"FROM #__attachments T1, #__content T2, #__daily_stats T3 ". 
									"WHERE T1.article_id = T2.id AND T2.id = T3.article_id AND T1.id = T3.attachment_id AND DATE_SUB(CURRENT_DATE,INTERVAL $gap DAY) = T3.date;";
	    		
		    	$rowsNumberForExistingAttachments = $this->executeQuery ( $db, $dailyStatsQuery );
    		}
	    	
			$entry = array ('LEVEL' => '1', 'STATUS' => 'INFO:', 'COMMENT' => "Stats for $today added in DB. $rowsNumberForNewAttachments rows inserted for new attachment(s). $rowsNumberForExistingAttachments rows inserted for existing attachments (gap filled: $gap day(s)). " );
			$log->addEntry($entry);
    	} else {
       		// daily_stats table is empty and must be bootstraped
       		$query= $this->dailyStatsBootstrapQuery;
	    	$rowsNumber = $this->executeQuery ( $db, $query);
//    		$this->executeQuery ( $db, $this->dailyStatsShiftDateQuery ); only for creating test data !!
	    	
			$entry = array ('LEVEL' => '1', 'STATUS' => 'INFO:', 'COMMENT' => "daily_stats table successfully bootstraped. $rowsNumber rows inserted");
			$log->addEntry($entry);
    	}
     }
	
	 private function executeQuery(JDatabase $db, $query) {
		$db->setQuery ( $query );
		$db->query ();
		
		if ($db->getErrorNum ()) {
			$query = $db->getErrorMsg ();
			//print_r( $e );
			JError::raiseError ( 500, $query );
			return;
		}
		
		return $db->getAffectedRows();
	 }

     private function loadResult(JDatabase $db, $query) {
    	$db->setQuery($query);
    	$res = $db->loadResult();
    	
    	if( $db->getErrorNum () ) {
			$e = $db->getErrorMsg();
			//print_r( $e );
			JError::raiseError( 500, $e );
			return null;
    	}
     	
    	return $res;
     }
	
     private function __construct() {
     	$this->statsQuery = "SELECT COUNT(filename) AS R_COUNT".
		    			 ", SUM(user_field_1) / 60 AS TOT_TIME".
		    			 ", SUM(download_count) AS DL_COUNT".
		    			 ", SUM(file_size) / 1000000 AS TOT_SIZE".
		    			 " FROM #__attachments;";
     	
     	$this->dailyStatsBootstrapQuery = "INSERT INTO #__daily_stats (article_id, attachment_id, date, total_hits_to_date, total_downloads_to_date) ".
						   "SELECT T1.article_id, T1.id, CURRENT_DATE, T2.hits, T1.download_count ".
						   "FROM #__attachments T1, #__content T2 ".
						   "WHERE T1.article_id = T2.id;";
     	
     	$this->dailyStatsCountQuery = "SELECT COUNT(id) FROM #__daily_stats;";
     	
     	$this->dailyStatsShiftDateQuery = "UPDATE #__daily_stats SET date=DATE_SUB(date,INTERVAL 1 DAY);";
     	
     	$this->dailyStatsMaxDateQuery = "SELECT MAX(date) FROM #__daily_stats;";
     	
     	$this->dailyStatsForNewAttachmentsQuery = 
       		"INSERT INTO #__daily_stats (article_id, attachment_id, date, total_hits_to_date, date_hits, total_downloads_to_date, date_downloads) 
       			SELECT T1.article_id, T1.id, CURRENT_DATE, T2.hits, T2.hits, T1.download_count, T1.download_count 
       			FROM #__attachments T1, #__content T2 
       			WHERE T1.article_id = T2.id AND T1.id IN ( 
       				SELECT T1.id 
       				FROM #__attachments T1 LEFT JOIN #__daily_stats ON T1.id = #__daily_stats.attachment_id 
       				WHERE #__daily_stats.attachment_id IS NULL);";
     }
}

?>
