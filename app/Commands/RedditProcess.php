<?php namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Structures\Link;

/**
 * Reddit Process Task
 *
 * Reads in cached submissions and filters them based
 * on the configured criteria, notifying for matches
 * and removing cached files when done.
 */
class RedditProcess extends BaseCommand
{
	protected $group       = 'Tasks';
	protected $name        = 'reddit:process';
	protected $description = 'Process cached Reddit comments and posts.';
	protected $usage       = 'reddit:process';

	public function run(array $params = [])
	{
		helper(['file', 'text']);

		foreach (get_filenames(config('Reddit')->directory, true) as $file)
		{
			if (basename($file) === 'index.html')
			{
				continue;
			}

			// Read in the file contents and attempt to unserialize it
			$contents = file_get_contents($file);
			try
			{
				$thing = unserialize($contents);
			}
			catch (\Throwable $e)
			{
				CLI::write('Error processing ' . basename($file) . ': ' . $e->getMessage());
				continue;
			}

			// Determine the fields by Kind
			$submission = [];
			switch (get_class($thing))
			{
				case Link::class:
					$submission = [
						'kind'      => (string) $thing,
						'name'      => $thing->name(),
						'author'    => $thing->author,
						'url'       => $thing->url,
						'thumbnail' => $thing->thumbnail,
						'title'     => $thing->title,
						'body'      => $thing->selftext,
						'html'      => $thing->selftext_html,
					];
				break;

				default:
					CLI::write('Skipping unsupported Kind: ' . get_class($thing), 'red');
					continue 2;
			}

			// Remove newlines to improve pattern matching.
			$search = trim(preg_replace('/\s+/', ' ', $submission['title'] . ' ' . $submission['body']));
			if (preg_match(config('Reddit')->pattern, $search, $matches))
			{
				// Gather the excerpt
				$submission['match']   = $matches[0];
				$submission['excerpt'] = excerpt($search, $submission['match']);

				// Print the header and highlighted version
				CLI::write($submission['kind'] . ' ' . $submission['name'] . ' ' . $submission['title'], 'green');
				CLI::write(highlight_phrase($submission['excerpt'], $submission['match'], "\033[0;33m", "\033[0m"));

				// Insert it into the database
				model(SubmissionModel::class)->insert($submission);
			}

			// Remove the file so it is not processed again
			if (ENVIRONMENT !== 'testing')
			{
				unlink($file);
			}
		}
	}
}
