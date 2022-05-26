<?php

namespace App\Actions;

use App\Entities\Submission;
use CodeIgniter\Email\Email;
use RuntimeException;
use Tatter\Outbox\Models\TemplateModel;

/**
 * Email Action Class
 *
 * Sends an email with a summary of
 * the provided Submission.
 */
class EmailAction implements ActionInterface
{
    /**
     * Default Template contents to pass to Email.
     * Can be overriden by $params
     *
     * @var array
     */
    protected $defaults = [
        'title'       => 'Reddit Mention',
        'contact'     => 'RedditBot',
        'unsubscribe' => 'Reply with "Unsubscribe"',
        'thumbnail'   => 'https://www.redditstatic.com/desktop2x/img/favicon/apple-icon-120x120.png',
    ];

    /**
     * Processes this Action for a specific Submission.
     *
     * @param array $params Any additional parameters
     *
     * @throws RuntimeException for any failures
     *
     * @return mixed Mostly for testing
     */
    public function execute(Submission $submission, array $params = [])
    {
        // Determine the data to send to the Email Template
        $data = [
            'name'    => $submission->name,
            'preview' => $submission->excerpt,
            'author'  => $submission->author,
            'url'     => $submission->url,
            'match'   => $submission->match,
            'kind'    => $submission->kind,
            'html'    => $submission->html,
        ];

        // Check for a valid thumbnail from the Submission itself
        if (filter_var($submission->thumbnail, FILTER_VALIDATE_URL) !== false) {
            $data['thumbnail'] = $submission->thumbnail;
        }

        // Add default values and apply any overrides from $params
        $data = array_merge($this->defaults, $data, $params['data'] ?? []);

        // Prep Email to our Template
        $template = model(TemplateModel::class)->findByName('Reddit Mention');
        $email    = $template->email($data);

        $email->setTo($params['recipients'] ?? config('Email')->recipients);

        if (! $email->send(false)) {
            throw new RuntimeException('Unable to send the email: ' . $email->printDebugger());
        }

        return $email;
    }
}
