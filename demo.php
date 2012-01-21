<?

#
#
#  BarleyDB example
#  www.barleydb.com
#
#	 @author adrianvera
#

require_once ('barley.class.php');

Barley::init('localhost', 'user', 'pass', 'db');
 
// Only use if you want to debug your querys
Barley::enableDebug();

// Execute your query
$result = Barley::query('SELECT * FROM `beers` WHERE barley = 200');

// If you want to see your debug info
$debugInfo = Barley::debugInfo();
Barley::debug($debugInfo);
