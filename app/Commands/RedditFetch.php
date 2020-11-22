<?php namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\Structures\Kind;
use Tatter\Reddit\Structures\Listing;

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

	/**
	 * Directory for storing submissions
	 *
	 * @var string
	 */
	protected $directory;

	/**
	 * Recent submission names to skip
	 *
	 * @var string[]
	 */
	protected $submissions;

	public function run(array $params = [])
	{
		$this->directory = rtrim(config('Reddit')->directory, '/ ') . DIRECTORY_SEPARATOR;

		// Preload the most recent submissions that have already been processed
		// to make sure we do not load them again
		$this->submissions = model(SubmissionModel::class)
			->orderBy('created_at', 'desc')
			->limit(100)
			->findColumn('name') ?? [];

		$reddit = service('reddit')->subreddit('heroesofthestorm');
		if ($after = cache('reddit_links_after'))
		{
			$reddit->after($after);
		}

		$listing = $reddit->fetch('new');
		if ($listing instanceof Listing)
		{
			$this->cacheLinks($listing);
		}
		else
		{
			CLI::write('Unexpected result from Reddit API: ' . get_class($listing));
		}

		// Store the after field for next run
		cache()->save('reddit_links_after', $listing->after);

	}

	/**
	 * Caches Links from an API Listing
	 *
	 * @param Listing $listing
	 */
	protected function cacheLinks(Listing $listing): void
	{
		/** @var Kind $thing */
		foreach ($listing as $thing)
		{
			$name = $thing->name();
			if (in_array($name, $this->submissions))
			{
				continue;
			}

			if (is_file($this->directory . $name))
			{
				continue;
			}

			file_put_contents($this->directory . $name, serialize($thing));
		}
	}
}
