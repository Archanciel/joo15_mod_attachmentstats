<?php

require_once dirname ( __FILE__ ) . '\..\..\lib\TestCase.php';

define('MOD_ATTACHMENTSTATS_BASE', JPATH_BASE . '\modules\mod_attachmentstats');

/**
 * Base class for all the mod_attachmentstats test classes.
 * 
 * Defining class as abstract is sementically correct and prevents MakeGood from executing
 * the test class !
 */
abstract class ModAttachmentstatsTestBase extends PHPUnit_Extensions_Database_TestCase {

	/**
	 * Sets the connection to the database
	 *
	 * @return connection
	 */
	protected function getConnection() {
// 		$dbURLPHP5_3_6_andLater = 'mysql:host=localhost;dbname=plucon15_dev;charset=UTF-8';
// 		$pdo = new PDO ( $dbURLPHP5_3_6_andLater, 'root', '' );

		$dbURLBeforePHP5_3_6 = 'mysql:host=localhost;dbname=pluscon15_dev';
		$options = array (PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' );
		$pdo = new PDO ( $dbURLBeforePHP5_3_6, 'root', '', $options );	// no pw for root !

		return $this->createDefaultDBConnection ( $pdo, 'plucon15_dev' );
	}

	protected function getSetUpOperation() {
		return $this->getOperations ()->INSERT ();
	}

	protected function getTearDownOperation() {
		return $this->getOperations ()->DELETE ();
	}
}

?>