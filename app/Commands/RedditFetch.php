<?php namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\Reddit;
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
	 * Reddit API client
	 *
	 * @var Reddit
	 */
	protected $reddit;

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
		$this->reddit    = service('Reddit');
		$this->directory = rtrim(config('Reddit')->directory, '/ ') . DIRECTORY_SEPARATOR;

		// Preload the most recent submissions that have already been processed
		// to make sure we do not load them again
		$this->submissions = model(SubmissionModel::class)
			->orderBy('created_at', 'desc')
			->limit(100)
			->findColumn('name') ?? [];

		// Check for new Links
		$this->cacheSubmissions($this->fetchSubmissions('new', 'reddit_last_link'));

		// Check for new Comments
		$this->cacheSubmissions($this->fetchSubmissions('comments', 'reddit_last_comment'));
	}

	/**
	 * Fetches a Listing of new submissions
	 *
	 * @param string $uri           URI to use for the submissions
	 * @param string|null $cacheKey Cache key to use for the "before" query
	 *
	 * @return Listing $listing
	 *
	 * @throws \RuntimeException
	 */
	protected function fetchSubmissions(string $uri, string $cacheKey = null): Listing
	{
		if ($cacheKey && $before = cache($cacheKey))
		{
			$this->reddit->before($before);
		}

		$listing = $this->reddit->fetch($uri);
		if (! $listing instanceof Listing)
		{
			throw new \RuntimeException('Unexpected result from Reddit API: ' . get_class($listing));
		}

		// Store the first result as the most recent
		if ($cacheKey && $kind = $listing->current())
		{
			/** @var Kind $kind */
			cache()->save($cacheKey, $kind->name());
		}

		return $listing;
	}

	/**
	 * Caches Things from an API Listing
	 *
	 * @param Listing $listing
	 */
	protected function cacheSubmissions(Listing $listing): void
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
