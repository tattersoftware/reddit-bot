<?php namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\Reddit;
use Tatter\Reddit\Structures\Kind;
use Tatter\Reddit\Structures\Listing;
use RuntimeException;

/**
 * Reddit Fetch Task
 *
 * Fetches all new submission from the subreddit
 * and stores them into a flat file for processing.
 */
class RedditFetch extends RedditCommand
{
	protected $name        = 'reddit:fetch';
	protected $description = 'Fetches new Reddit comments and posts since last run.';
	protected $usage       = 'reddit:fetch';
	protected $arguments   = [];

	/**
	 * Recent submission names to skip
	 *
	 * @var string[]
	 */
	protected $submissions;

	public function run(array $params = [])
	{
		// Preload the most recent submissions that have already been processed
		// to make sure we do not load them again
		$this->submissions = model(SubmissionModel::class)
			->orderBy('created_at', 'desc')
			->limit(100)
			->findColumn('name') ?? [];

		// Track Subreddits so we only poll them once
		$subreddits = [];
		foreach ($this->directives->findAll() as $class)
		{
			$directive = new $class();
			foreach ($directive->subreddits as $subreddit)
			{
				if (in_array($subreddit, $subreddits))
				{
					continue;
				}
		
				// Check for new Links
				$this->write($this->fetch('r/' . $subreddit . '/new'));

				// Check for new Comments
				$this->write($this->fetch('r/' . $subreddit . '/comments'));

				$subreddits[] = $subreddit;
			}
		}
	}

	/**
	 * Fetches a Listing of new Things
	 *
	 * @param string $uri URI to use for the submissions
	 *
	 * @return Listing $listing
	 *
	 * @throws RuntimeException
	 */
	protected function fetch(string $uri): Listing
	{
		// Create a cache-safe key
		$cacheKey = preg_filter('/[^A-Za-z_]+/', '', $uri);

		// Check for a previous request to follow
		if ($before = cache($cacheKey))
		{
			$this->reddit->before($before);
		}

		$listing = $this->reddit->fetch($uri);
		if (! $listing instanceof Listing)
		{
			throw new RuntimeException('Unexpected result from Reddit API: ' . get_class($listing));
		}

		// Store the first result as the most recent
		if ($kind = $listing->current())
		{
			/** @var Kind $kind */
			cache()->save($cacheKey, $kind->name());
		}

		return $listing;
	}

	/**
	 * Writes Things from an API Listing
	 *
	 * @param Listing $listing
	 */
	protected function write(Listing $listing): void
	{
		/** @var Kind $kind */
		foreach ($listing as $kind)
		{
			$name = $kind->name();
			if (in_array($name, $this->submissions))
			{
				continue;
			}

			if (is_file($this->directory . $name))
			{
				continue;
			}

			file_put_contents($this->directory . $name, serialize($kind));
		}
	}
}
