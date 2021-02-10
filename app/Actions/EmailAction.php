<?php namespace App\Actions;

use App\Entities\Submission;

/**
 * Email Action Class
 *
 * Sends an email with a summary of
 * the provided Submission.
 */
class EmailAction implements ActionInterface
{
	/**
	 * Processes this Action for a specific Submission.
	 *
	 * @param Submission $submission
	 * @param array $params Any additional parameters
	 */
	public function execute(Submission $submission, array $params = [])
	{
	}
}
