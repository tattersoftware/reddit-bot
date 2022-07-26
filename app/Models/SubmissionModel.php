<?php

namespace App\Models;

use App\Entities\Submission;
use CodeIgniter\Model;
use RuntimeException;
use Tatter\Reddit\Structures\Kind;

class SubmissionModel extends Model
{
    protected $table          = 'submissions';
    protected $primaryKey     = 'id';
    protected $returnType     = Submission::class;
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $skipValidation = true;
    protected $allowedFields  = [
        'directive',
        'subreddit',
        'kind',
        'name',
        'author',
        'url',
        'thumbnail',
        'title',
        'body',
        'html',
        'match',
        'excerpt',
        'executed_at',
    ];

    /**
     * Converts a Kind to an array for Submissions
     *
     * @throws RuntimeException for Kind that is not a Comment or Link
     */
    public function fromKind(Kind $kind): array
    {
        $row = [
            'subreddit' => $kind->subreddit,
            'kind'      => (string) $kind,
            'name'      => $kind->name(),
            'author'    => $kind->author,
            'url'       => 'https://www.reddit.com' . $kind->permalink,
            'thumbnail' => filter_var($kind->thumbnail ?? '', FILTER_VALIDATE_URL) ? $kind->thumbnail : null,
        ];

        // Add Kind-specific fields
        switch ((string) $kind) {
            case 'Comment':
                $row = array_merge($row, [
                    'title' => $kind->link_title,
                    'body'  => $kind->body,
                    'html'  => $kind->body_html,
                ]);
                break;

            case 'Link':
                $row = array_merge($row, [
                    'title' => $kind->title,
                    'body'  => $kind->selftext,
                    'html'  => $kind->selftext_html,
                ]);
                break;

            default:
                throw new RuntimeException('Unsupport Kind:' . get_class($kind));
        }

        return $row;
    }
}
