<?php namespace App\Entities;

use CodeIgniter\Entity;

class Submission extends Entity
{
	protected $table = 'submissions';

	protected $dates = [
		'created_at',
		'updated_at',
	];

	protected $casts = [
		'notified' => 'boolean',
	];
}
