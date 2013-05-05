    <?php
    			error_reporting(0);
    			
				/* Initialize Joomla framework */
				define ( '_JEXEC', 1 );
				
				/* Setting JPATH_BASE to the Joomla root dir provided the current file is stored in <Joomla root dir>\test ! */
				define ( 'DS', DIRECTORY_SEPARATOR );
				define ( 'JPATH_BASE', str_replace ( DS . 'modules' . DS . 'mod_attachmentstats', '', dirname ( __FILE__ ) ) );
				
				/* Required Files */
				require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
				require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');
				require_once (JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php');
				
				/* Create the Application */
				$mainframe = & JFactory::getApplication ( 'site' );

				$fromId = JRequest::getVar ( 'fromid', 1 );
				$query = "SELECT * FROM  `#__daily_stats` WHERE `id` >= $fromId";
												
				/* @var $db JDataBase */
				$db = JFactory::getDBO ();
				$db->setQuery ( $query );
				$result = $db->loadAssocList();
				
				/* Die on error */
				if ($db->getErrorMsg ()) {
					echo $db->getErrorMsg ();
					die ();
				}
				
				print(json_encode($result));				
	?>
