<?

/*
 *	Barley database library
 *	www.barleydb.com
 *
 *	@author adrianvera // @adrianveracom // adrian [at] code4play [dot] com
 *  
 */


class Barley 
{

	//Barley vars section
	private static $instance;
	private $link;
	private static $debug = false;
	private static $debugQuerys = array();


	private function __construct($host, $user, $pass, $name)
	{
		try
		{
			$this->connect($host, $user, $pass, $name);	
		}
		catch(Exception $e)
		{
			self::exception($e);
		}
	}

	public static function init($host, $user, $pass, $name)
	{
		if ( !isset(self::$instance)) 
		{
			$class = __CLASS__;
			self::$instance = new $class($host, $user, $pass, $name);
		}
		return self::$instance;	
	}


	private function connect($host, $user, $pass, $name)
	{
		$this->link = mysql_connect($host, $user, $pass);
		if (!$this->link)
		{
			throw new Exception ('We have some problems to connect with database (mysql says : ' . mysql_error(). ')');
		}

		$db_selected = mysql_select_db($name, $this->link);
		if (!$db_selected)
		{
			throw new Exception ('We can\'t use the ' . $name . ' database (mysql says : ' . mysql_error(). ')');			
		}
	}

	public static function query($query)
	{	
		try
		{
			$start = self::$debug ? microtime() : 0;
			$result = self::execute(self::prepare($query));
			$end = self::$debug ? microtime() : 0;

			if (self::$debug)
			{
				self::debugQuery($query,$start,$end);
			}

			return $result;
		}
		catch(Exception $e)
		{
			self::exception($e);
		}
	
	}

	private static function prepare($query)
	{
		$prepareExpr = '/{(.*)}/U';

		preg_match_all($prepareExpr, $query, $matches);

		for ($i = 0; $i < count($matches[0]); $i++)
		{
			$param = mysql_real_escape_string($matches[1][$i]);
			$query = str_replace ($matches[0][$i], $param, $query);
		}
		
		return $query;
	}


	private function execute($query)
	{	
		$result = mysql_query($query);
		if (!$result)
		{
			throw new Exception ('Invalid query (mysql says : ' . mysql_error(). ')');			
		}

		$dataFetched = array();

		if (!empty($result))
		{
			while ( $data = mysql_fetch_object($result))
			{
				$dataFetched[] = $data; 
			}
			return $dataFetched;
		}
		else
		{
			return null;		
		}
	}

	public static function enableDebug()
	{
		self::$debug = true;
	}

	private function debugQuery($query,$start,$end)
	{
		self::$debugQuerys[] = array (
			'query' => $query,
			'time' => $end - $start
		);
	}

	public static function debugInfo()
	{
		try
		{
			if (!self::$debug)
			{
				throw new Exception ('You have to enable debug mode to show debug info');
			}
			else
			{			
				return self::$debugQuerys;
			}	
		}
		catch (Exception $e)
		{
			self::exception($e);
		}	
	}

	public static function debug($data)
	{
		var_dump($data);
	}

	private static function exception($e)
	{
		die('BarleyException:: ' . $e->getMessage() . "\n");
	}

}
