<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Submission extends Entity
{
	protected $table = 'submissions';

	protected $dates = [
		'created_at',
		'updated_at',
		'executed_at',
	];
}
