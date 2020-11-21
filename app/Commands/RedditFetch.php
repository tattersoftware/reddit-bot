<?php namespace App\Commands;

use App\Models\DungeonModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Exceptions\RedditException;

/**
 * Reddit Fetch Task
 *
 * Fetches all new submission from the subreddit
 * and stores them into a flat file for processing.
 */
class RedditFetch extends BaseCommand
{
	protected $group       = 'Tasks';
	protected $name        = 'reddit:fetch';
	protected $description = 'Fetches new Reddit comments and posts since last run.';
	protected $usage       = 'reddit:fetch';
	protected $arguments   = [];

	public function run(array $params = [])
	{
		$reddit = service('reddit');
		$things = $reddit->subreddit('heroesofthestorm')->fetch('new');

		foreach ($things as $thing)
		{
			$file = WRITEPATH . 'submissions' . DIRECTORY_SEPARATOR . $thing->name();
			if (is_file($file))
			{
				continue;
			}

			file_put_contents($file, serialize($thing));
		}
	}
}
