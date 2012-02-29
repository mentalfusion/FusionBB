<?php
  /**
   * Factory Class for FusionBB
   * ---
   * This will take any URI parameters and use those to call the proper functions for
   * displaying content within the forum.
   **/

  class Factory 
  {
	// define our forum and thread id variables
	private $forum_id = 0;
	private $thread_id = 0;
	public $conf;
	public $db;
	public $base_dir = '';
	public $uri_args;

	// We need to grab the URI and parse it.  The structure is index.php/(sub)forum-forumID/thread-topicID
	// the gist of how it should work is: grab the topic ID and display any breadcrumbs based on the
	// location of the topic. If no topic is present, do the same but using the forum information and
	// display the subforum list or topic list (or both) for the requested forum.  If neither are present
	// display the index page.
	public function __construct($conf=array())
	{
		// default error; if the config is empty, something is wrong:
		if (empty($conf))
		{
			echo 'Fatal Error: Config variables missing.';
			return false;
		}
		else
		{
			$this->conf = (object)$conf;
		}

		// establish our base directory:
		$this->base_dir = '/usr/local/www/mentalfusion.org/FusionBB/';

		if ($this->conf->db_driver != '')
		{
			require ($this->base_dir.'system/drivers/'.$this->conf->db_driver.'.php');
			// tmp driver var:
			$db_driver = $this->conf->db_driver;

			$this->db = $db_driver::objCreate(
				$this->conf->dbhost,
				$this->conf->dbuser,
				$this->conf->dbpass,
				$this->conf->dbname
			);
		}

		// parse the URI and set our id variables:
		$tmp_uri = explode('index.php/',$_SERVER['PHP_SELF']);
		// tmp_uri[1] contains our required info; could be blank:
		if (strpos($tmp_uri[1],'/') !== false)
		{
			$uri_params = explode('/',$tmp_uri[1]);
			$tmp_forum = explode('-',$uri_params[0]);
			$tmp_thread = explode('-',$uri_params[1]);

			// pop the IDs off the end of our thread and forum arrays
			$this->forum_id = (count($tmp_forum) > 1) ? array_pop($tmp_forum) : 0;
			$this->thread_id = (count($tmp_thread) > 1) ? array_pop($tmp_thread) : 0;

			// go through and add each uri parameter to the list of uri_params
			// these may have view-specific uses (such as page numbers, etc):
			array_unshift($uri_params,$_SERVER['PHP_SELF']); // add SELF to the first param
			$this->uri_args = new stdClass;
			foreach ($uri_params as $key => $value) {
				$arg_key = 'arg'.$key;
				$this->uri_args->$arg_key = $value;
			}
		}
		else if ($tmp_uri[1] != '')
		{
			$tmp_forum = explode('-',$tmp_uri[1]);

			// pop the ID off the end of our forum array
			$this->forum_id = (count($tmp_forum) > 1) ? array_pop($tmp_forum) : 0;
		}
	} // don't need to do anything here (for now?)

	public function __destruct() {} // nothing needed here (for now?)

	// take what we gathered upon construction and use it to return a view for rendering
	public function getView()
	{
		// if a thread_id is set, we need to get the thread view
		if ($this->thread_id)
		{
			return 'Thread_view';
		}
		// if a forum id is set, we need to get the forum view
		else if ($this->forum_id)
		{
			return 'Forum_view';
		}
		// if neither are set, get the index view 
		else
		{
			return 'Index_view';
		}
	}

	// render our view; this is as simple as calling our getView() function and including the view file:
	public function renderView()
	{
	}
  }
?>
