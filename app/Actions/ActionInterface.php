<?php

namespace App\Actions;

use App\Entities\Submission;
use RuntimeException;

/**
 * Action Interface
 *
 * Handles responses defined by
 * a Directive.
 */
interface ActionInterface
{
    /**
     * Processes this Action for a specific Submission.
     *
     * @param array $params Any additional parameters
     *
     * @throws RuntimeException for any failures
     *
     * @return mixed Mostly for testing
     */
    public function execute(Submission $submission, array $params = []);
}
