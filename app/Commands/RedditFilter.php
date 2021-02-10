<?php namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Structures\Kind;
use Throwable;

/**
 * Reddit Process Task
 *
 * Reads in cached submissions and filters them based
 * on the configured criteria, notifying for matches
 * and removing cached files when done.
 */
class RedditFilter extends RedditCommand
{
	protected $group       = 'Tasks';
	protected $name        = 'reddit:filter';
	protected $description = 'Filter stored Reddit comments and posts.';
	protected $usage       = 'reddit:filter';

	public function run(array $params = [])
	{
		helper(['file', 'text']);

		foreach (get_filenames(config('Project')->submissionsPath, true) as $file)
		{
			if (basename($file) === 'index.html')
			{
				continue;
			}

			// Read in the file contents and attempt to unserialize it
			$contents = file_get_contents($file);
			try
			{
				/** @var Kind $kind */
				$kind = unserialize($contents);
			}
			catch (Throwable $e)
			{
				CLI::write('Error processing ' . basename($file) . ': ' . $e->getMessage());
				continue;
			}

			// Convert the Kind to Submission
			$submission = [
				'subreddit' => $kind->subreddit,
				'kind'      => (string) $kind,
				'name'      => $kind->name(),
				'author'    => $kind->author,
			];

			// Add Kind-specific fields
			switch ((string) $kind)
			{
				case 'Comment':
					$submission = array_merge($submission, [
						'url'       => $kind->link_url . $kind->id,
						'title'     => $kind->link_title,
						'body'      => $kind->body,
						'html'      => $kind->body_html,
					]);
				break;

				case 'Link':
					$submission = array_merge($submission, [
						'url'       => $kind->url,
						'thumbnail' => $kind->thumbnail,
						'title'     => $kind->title,
						'body'      => $kind->selftext,
						'html'      => $kind->selftext_html,
					]);
				break;

				default:
					log_message('error', 'Unsupport Kind in Reddit Filter:' . get_class($kind));
					CLI::write('Skipping unsupported Kind: ' . get_class($kind), 'red');
					continue 2;
			}

			// Remove newlines to improve pattern matching.
			$search = trim(preg_replace('/\s+/', ' ', $submission['title'] . ' ' . $submission['body']));

			// Check each Directive for a match
			foreach ($this->directives as $directive)
			{
				// Make sure this is a subreddit for this Directive
				if (! in_array($submission['subreddit'], $directive->subreddits))
				{
					continue;
				}

				// Check each pattern individually so we can highlight the first match
				foreach ($directive->patterns as $pattern)
				{
					if (preg_match($pattern, $search, $matches))
					{
						// Gather the excerpt
						$submission['directive'] = $directive->uid;
						$submission['match']     = $matches[0];
						$submission['excerpt']   = excerpt($search, $submission['match']);

						// Print the header and highlighted version
						CLI::write($submission['kind'] . ' ' . $submission['name'] . ' ' . $submission['title'], 'green');
						CLI::write(highlight_phrase($submission['excerpt'], $submission['match'], "\033[0;33m", "\033[0m"));

						// Insert it into the database
						model(SubmissionModel::class)->insert($submission);

						// Skip to the next Directive
						continue 2;
					}
				}
			}

			// Remove the file so it is not processed again
			unlink($file);
		}
	}
}
