<?php

namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;
use RuntimeException;

/**
 * Reddit Execute Task
 *
 * Loads submissions from the database
 * that have not yet been executed and
 * sends notifications from configured
 * handlers.
 */
class RedditExecute extends RedditCommand
{
    protected $group       = 'Tasks';
    protected $name        = 'reddit:execute';
    protected $description = 'Executes actions on filtered Reddit submissions.';
    protected $usage       = 'reddit:execute';

    public function run(array $params = [])
    {
        foreach (model(SubmissionModel::class)->where('executed_at IS NULL')->findAll() as $submission) {
            CLI::write('Executing Actions for ' . $submission->name);

            if (! isset($this->directives[$submission->directive])) {
                throw new RuntimeException('Unknown Directive: ' . $submission->directive);
            }
            $directive = $this->directives[$submission->directive];

            foreach ($directive->actions as $i => $class) {
                (new $class())->execute($submission, $directive->params[$i] ?? []);
            }

            // Mark them as executed as we go in case tasks are run in parallel
            model(SubmissionModel::class)->update($submission->id, ['executed_at' => Time::now()->toDateTimeString()]);
        }
    }
}
