<?php namespace App\Actions;

use App\Entities\Submission;

/**
 * Action Interface
 *
 * Handles responses defined by
 * a Directive.
 */
interface Action
{
	/**
	 * Processes this Action for a specific Submission.
	 *
	 * @param Submission $submission
	 * @param array $params Any additional parameters
	 */
	public function execute(Submission $submission, array $params = []);
}
