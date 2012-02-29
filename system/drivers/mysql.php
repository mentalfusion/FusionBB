<?php
  class mysql {
	// set up our static instance variable
	private static $instance;

	// set up our privatized login variables
	private static $dbhost, $dbuser, $dbpass, $dbname, $conn, $resource;

	private function __construct() {} // privatized; gonna try to Singleton this

	public static function objCreate($dbhost, $dbuser, $dbpass, $dbname)
	{
		// configure database variables:
		self::$dbhost = $dbhost;
		self::$dbuser = $dbuser;
		self::$dbpass = $dbpass;
		self::$dbname = $dbname;

		if (!self::_connect(1))
		{
			// don't proceed if we can't even connect to the database
			return false;
		}

		// check for an existing instance:
		if (!isset(self::$instance))
		{
			$class = __CLASS__;
			self::$instance = new $class;
		}

		return self::$instance;
	}

	/**
	 *
	 * _connect opens a connection to our MySQL database
	 * param test, when set to 1, will close the connection immediately after opening for testing purposes
	 *
	 **/
	private function _connect($test=0)
	{
                // check to see if we can connect with this information:
                if ((self::$conn = mysql_connect(self::$dbhost,self::$dbuser,self::$dbpass))) 
                {
                        // can we select the database?
                        if (!mysql_select_db(self::$dbname,self::$conn))
                        {
				// couldn't use requested database
                                echo 'Failed to connect to database "'.self::$dbname.'": '.mysql_error();
                                return false;
                        }

                        // close the test connection if we're just testing
			if ($test)
			{
	                        self::_disconnect();
			}
                }
                else
                {
			// couldn't connect to database
                        echo 'Failed to connect to MySQL database: '.mysql_error();
                        return false;
                }

		// return true if no issues
		return true;
	}

	private function _disconnect()
	{
		return mysql_close(self::$conn);
	}

	// actually performs the query and returns the resource ID
	private function _execute($query)
	{
		$res = mysql_query($query) or self::_error(mysql_error());

		return $res;
	}

	private function _error($error)
	{
		// display the error
		echo $error;

		// negative return
		return false;
	}

	// returns all rows in a table in object form:
	public function get($table)
	{
		// connect to the database
		self::_connect();

		// grab all rows from the database for the given $table:
		$res = self::_execute('SELECT * FROM `'.mysql_real_escape_string($table).'`');

                // if $res isn't false, set the resource in our object
                if ($res)
                {
                        self::$resource = $res;
                }
                // unset any existing resource on a failed query
                else if (isset(self::$resource))
                {
                        unset(self::$resource);
                }

		// close the connection
		self::_disconnect();
	}

	// normal query function, simply sets the query up in the object
	public function query($query)
	{
		// connect to the database
		self::_connect();

		// execute the query
		$res = self::_execute($query);

		// if $res isn't false, set the resource in our object
		if ($res)
		{
			self::$resource = $res;
		}
		// unset any existing resource on a failed query
		else if (isset(self::$resource))
		{
			unset(self::$resource);
		}

		// close the connection
		self::_disconnect();
	}

	// pull the results of a query into an object using our resource static variable
	public function results()
	{
		// using any existing $resource, process the results into a stdClass object:
		$out = array();

		// if the resource exists, proceed:
		if (isset(self::$resource))
		{
			// loop through the results and add them to the object:
			while ($row = mysql_fetch_assoc(self::$resource))
			{
				$out[] = (object)$row;
			}
		}

		// return $out as an object:
		return (object)$out;
	}
  }
?>
