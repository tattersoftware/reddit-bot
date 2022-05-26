<?php namespace App\Actions;

use App\Entities\Submission;
use CodeIgniter\Email\Email;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Tatter\Pushover\Entities\Message;
use RuntimeException;

/**
 * Push Action Class
 *
 * Sends a push notification using Pushover
 * email with a summary of the provided Submission.
 */
class PushAction implements ActionInterface
{
	/**
	 * Processes this Action for a specific Submission.
	 *
	 * @param array $params Any additional parameters
	 *
	 * @return Message Mostly for testing
	 * @throws RuntimeException for any failures (PushoverException)
	 */
	public function execute(Submission $submission, array $params = []): Message
	{
		// Apply any overrides to the config
		$config = config('Pushover');
		foreach ($params['config'] ?? [] as $key => $value)
		{
			$config->$key = $value;
		}

		// Get the configuration-specific client
		$client = Services::pushover($config, null, false);

		// Add default values and apply any overrides from $params
		$data = [
			'title'     => 'Reddit mention by ' . $submission->author,
			'message'   => $submission->excerpt,
			'html'      => 0,
			'url'       => $submission->url,
			'url_title' => substr($submission->title, 0, 99), // Pushover's limit
		];
		$data = array_merge($data, $params['data'] ?? []);

		// Create the Message
		$message = $client->message($data);

		// Try to download the thumbnail and use it as an attachment
		$thumbnailUrl = $params['thumbnail'] ?? $submission->thumbnail;
		if (filter_var($thumbnailUrl, FILTER_VALIDATE_URL) !== false)
		{
			$thumbnail = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . basename($thumbnailUrl);

			if (($contents = file_get_contents($thumbnailUrl)) && file_put_contents($thumbnail, $contents))
			{
				$message->attachment = $thumbnail;
			}
		}

		$message->send();
		return $message;
	}
}
