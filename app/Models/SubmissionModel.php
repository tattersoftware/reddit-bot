<?php namespace App\Models;

use App\Entities\Submission;
use CodeIgniter\Model;
use Tatter\Reddit\Structures\Kind;
use RuntimeException;

class SubmissionModel extends Model
{
	protected $table      = 'submissions';
	protected $primaryKey = 'id';
	protected $returnType = Submission::class;

	protected $useSoftDeletes = false;
	protected $useTimestamps  = true;
	protected $skipValidation = true;

	protected $allowedFields = [
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
	 * @param Kind $kind
	 *
	 * @return array
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
		];

		// Add Kind-specific fields
		switch ((string) $kind)
		{
			case 'Comment':
				$row = array_merge($row, [
					'url'       => $kind->link_url . $kind->id,
					'title'     => $kind->link_title,
					'body'      => $kind->body,
					'html'      => $kind->body_html,
				]);
			break;

			case 'Link':
				$row = array_merge($row, [
					'url'       => $kind->url,
					'thumbnail' => $kind->thumbnail,
					'title'     => $kind->title,
					'body'      => $kind->selftext,
					'html'      => $kind->selftext_html,
				]);
			break;

			default:
				throw new RuntimeException('Unsupport Kind:' . get_class($kind));
		}

		return $row;
	}
}
