<?php namespace App\Commands;

use App\Models\SubmissionModel;
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
		// Preload the most recent submissions that have already been processed
		// to make sure we do not load them again
		$submissions = model(SubmissionModel::class)
			->orderBy('created_at', 'desc')
			->limit(100)
			->findColumn('name') ?? [];

		$directory = rtrim(config('Reddit')->directory, '/ ') . DIRECTORY_SEPARATOR;
		$reddit    = service('reddit');
		$things    = $reddit->subreddit('heroesofthestorm')->fetch('new');

		foreach ($things as $thing)
		{
			$name = $thing->name();
			if (in_array($name, $submissions))
			{
				continue;
			}

			if (is_file($directory . $name))
			{
				continue;
			}

			file_put_contents($directory . $name, serialize($thing));
		}
	}
}
