<?php namespace App\Actions;

use App\Entities\Submission;

/**
 * Session Action Class
 *
 * Adds a reference to the Submission
 * into the Session. Mostly used for testing
 * and development.
 */
class SessionAction implements ActionInterface
{
	/**
	 * Processes this Action for a specific Submission.
	 *
	 * @param array $params Any additional parameters
	 */
	public function execute(Submission $submission, array $params = [])
	{
		if (! isset($_SESSION['submissions']))
		{
			$_SESSION['submissions'] = [];
		}

		$_SESSION['submissions'][] = $submission;
	}
}
