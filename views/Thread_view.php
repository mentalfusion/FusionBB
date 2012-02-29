<?php
  class Thread_view
  {
	public $uri_args;
	private $thread_id, $forum_id, $db, $conf, $num_per_page;

	public function __construct($fusionbb)
	{
		// configure any arguments passed in via the URI
		$uri_args = $fusionbb->uri_args;
		$this->forum_id = (isset($uri_args->arg1)) ? $uri_args->arg1 : 0;
		$this->thread_id = (isset($uri_args->arg2)) ? $uri_args->arg2 : 0;
		$this->page = (isset($uri_args->arg3) && is_numeric($uri_args->arg3) && $uri_args->arg3 > 0) ? $uri_args->arg3 : 1;

		// configure the db method object
		$this->db = $fusionbb->db;

		$this->replies_per_page = $fusionbb->conf->replies_per_page;
	}

	public function render()
	{
		// test mysql driver
$query='SELECT u.username, p.title, p.body
          FROM fbb_posts as p
    RIGHT JOIN fbb_users as u
            ON p.user_id = u.id
         WHERE ';

$query .= ' LIMIT '.mysql_real_escape_string(($this->replies_per_page*($this->page - 1))).',2');

		// echo the results
		foreach ($this->db->results() as $row)
		{
			echo $row->id.' - '.$row->title."<br />";
		}
	}

  }
?>
