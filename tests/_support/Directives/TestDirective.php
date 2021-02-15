<?php namespace Tests\Support\Directives;

use App\Actions\SessionAction;
use App\BaseDirective;

class TestDirective extends BaseDirective
{
	/**
	 * Attributes for Tatter\Handlers
	 *
	 * @var array<string, mixed>      
	 */
	public $attributes = [
		'name'       => 'Test Directive', // Name for this Action
		'uid'        => 'test_directive', // Unique identifier
		'rate'       => MINUTE, // How frequently to check
		'subreddits' => ['heroesofthestorm'], // Array of strings, the portion after "/r/"
		'patterns'   => [ // Regex patterns to match
			'/Heroes.?Profile/i',
			'/Heroes.?Share/',
			'/Hots.?Api/i',
			'/Hots.?Logs/i',
		],
		'actions'    => [SessionAction::class], // Actions to use during execution
		'params'     => [], // Arrays of parameters to pass to each Action
	];
}
