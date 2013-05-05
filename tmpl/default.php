<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
require_once (MOD_ATTACHMENTSTATS_BASE.DS.'constants.php');
$labels = array(REC_COUNT_POS => 'Nb enreg',DOWNLOAD_COUNT_POS => 'Nb téléch',LISTENING_TOTAL_TIME_POS => 'Durée totale',REC_TOTAL_SIZE_POS => 'Taille totale');
?>
<table style="width: 100%;" class="table_stats" border="0" align="center"> 
	<tbody>
<?php
foreach ($stats_array as $key => $value) {
	echo '<tr><td style="width: 50%; text-align: left;">';
	echo $labels[$key];
	echo '</td><td style="width: 50%; text-align: right;">';
	echo $value;
	echo '</td></tr>';
}
?>
	</tbody>
</table>

