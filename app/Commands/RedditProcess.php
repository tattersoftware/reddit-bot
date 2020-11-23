<?php namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Structures\Kind;

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
				/** @var Kind $kind */
				$kind = unserialize($contents);
			}
			catch (\Throwable $e)
			{
				CLI::write('Error processing ' . basename($file) . ': ' . $e->getMessage());
				continue;
			}

			// Convert the Kind to Submission
			$submission = [];
			switch ((string) $kind)
			{
				case 'Comment':
					$submission = [
						'kind'      => (string) $kind,
						'name'      => $kind->name(),
						'author'    => $kind->author,
						'url'       => $kind->link_url . $kind->id,
						'title'     => $kind->link_title,
						'body'      => $kind->body,
						'html'      => $kind->body_html,
					];
				break;

				case 'Link':
					$submission = [
						'kind'      => (string) $kind,
						'name'      => $kind->name(),
						'author'    => $kind->author,
						'url'       => $kind->url,
						'thumbnail' => $kind->thumbnail,
						'title'     => $kind->title,
						'body'      => $kind->selftext,
						'html'      => $kind->selftext_html,
					];
				break;

				default:
					log_message('error', 'Unsupport Kind in Reddit Process:' . get_class($kind));
					CLI::write('Skipping unsupported Kind: ' . get_class($kind), 'red');
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
			unlink($file);
		}
	}
}
