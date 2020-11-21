<?php namespace App\Models;

use App\Entities\Submission;
use CodeIgniter\Model;

class SubmissionModel extends Model
{
	protected $table      = 'submissions';
	protected $primaryKey = 'id';
	protected $returnType = Submission::class;

	protected $useSoftDeletes = false;
	protected $useTimestamps  = true;
	protected $skipValidation = true;

	protected $allowedFields = [
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
		'notified',
	];
}
