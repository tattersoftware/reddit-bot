<?php namespace App\Commands;

use App\Entities\Submission;
use App\Models\SubmissionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Email\Email;
use Tatter\Outbox\Models\TemplateModel;
use Tatter\Pushover\Exceptions\PushoverException;

/**
 * Reddit Notify Task
 *
 * Loads submissions from the database
 * that have not yet been notified and
 * sends notifications from configured
 * handlers.
 */
class RedditNotify extends BaseCommand
{
	protected $group       = 'Tasks';
	protected $name        = 'reddit:notify';
	protected $description = 'Send notifications for matched Reddit submissions.';
	protected $usage       = 'reddit:notify';

	public function run(array $params = [])
	{
		$notified = [];
		foreach (model(SubmissionModel::class)->where('notified', 0)->findAll() as $submission)
		{
			CLI::write('Sending notifications for ' . $submission->name);

			$this->email($submission);

			// Check if Pushoer is configured
			if (config('Pushover')->user)
			{
				$this->push($submission);
			}

			// Mark them as notified
			$notified[] = $submission->id;
		}

		if (count($notified))
		{
			model(SubmissionModel::class)->update($notified)->update($notified, ['notified' => 1]);
		}
	}

	/**
	 * Sends an email based on $submission
	 *
	 * @param Submission $submission
	 */
	protected function email(Submission $submission): void
	{
		$template = model(TemplateModel::class)->findByName('Reddit Mention');

		// Prep Email to our Template
		$email = $template->email([
			'name'        => $submission->name,
			'title'       => 'Reddit Mention',
			'preview'     => $submission->excerpt,
			'contact'     => 'Heroes Share',
			'unsubscribe' => 'Reply with "Unsubscribe"',
			'author'      => $submission->author,
			'match'       => $submission->match,
			'kind'        => $submission->kind,
			'html'        => $submission->html,
			'thumbnail'   => filter_var($submission->thumbnail, FILTER_VALIDATE_URL) === false
				? 'https://heroesshare.net/assets/img/logo-small.png'
				: $submission->thumbnail,
		]);

		$email->setTo(config('Email')->recipients);

		if (! $email->send(false))
		{
			log_message('error', 'Unable to send an email: ' . $email->printDebugger());
		}
	}

	/**
	 * Sends a push notification to Pushover based on $submission
	 *
	 * @param Submission $submission
	 */
	protected function push(Submission $submission): void
	{
		$message = service('pushover')->message([
			'title'     => 'Reddit mention by ' . $submission->author,
			'message'   => $submission->excerpt,
			'html'      => 0,
			'url'       => $submission->url,
			'url_title' => $submission->title,
		]);

		// Try to download the thumbnail and use it as an attachment
		if (filter_var($submission->thumbnail, FILTER_VALIDATE_URL) !== false)
		{
			$thumbnail = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . basename($submission->thumbnail);

			if ($contents = file_get_contents($submission->thumbnail))
			{
				if (file_put_contents($thumbnail, $contents))
				{
					$message->attachment = $thumbnail;
				}
			}
		}

		try
		{
			$message->send();
		}
		catch (PushoverException $e)
		{
			log_message('error', 'Unable to send Pushover notification:');
			foreach (service('pushover')->getErrors() as $error)
			{
				log_message('error', $error);
			}
		}
	}
}
