<?php

require_once dirname ( __FILE__ ) . '..\..\baseclass\ModAttachmentstatsTestBase.php';
require_once MOD_ATTACHMENTSTATS_BASE . '\helper.php';
require_once MOD_ATTACHMENTSTATS_BASE . '\constants.php';

/**
 * This class tests the modAttachmentStatsHelper->getStats() on an attachments table containing
 * 1 published and 1 unpublished attachments.
 *  
 * @author Jean-Pierre
 *
 */
class OnePubAttachOneUnpubAttachTest extends ModAttachmentstatsTestBase {
	/**
	 * tests the modAttachmentStatsHelper->getStats() on an attachments table containing
     * 1 published and 1 unpublished attachments.
	 */
	public function testGetStats1PubAttach1UnpubAttach() { 
		$stats_array = modAttachmentStatsHelper::getInstance()->getStats(NULL, '#__attachments_mod_attachmentstats_test');
		
		$this->assertEquals(sizeof($stats_array),4);
 		$this->assertEquals(1,$stats_array[REC_COUNT_POS],'rec counts');
 		$this->assertEquals(10,$stats_array[DOWNLOAD_COUNT_POS],'download counts');
 		$this->assertEquals('1 H 40\'',$stats_array[LISTENING_TOTAL_TIME_POS],'total length');
 		$this->assertEquals('10 MB',$stats_array[REC_TOTAL_SIZE_POS],'total size');
	}
	
	/**
	 * Gets the data set to be loaded into the database during setup.
	 * 
	 * @return xml dataset
	 */
	protected function getDataSet() {
		return $this->createXMLDataSet ( dirname ( __FILE__ ) . '\..\data\1_pub_attach_1_unpub_attach.xml' );
	}
}

?>