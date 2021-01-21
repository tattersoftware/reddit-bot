<?php namespace Tests\Support\Directives;

use App\Actions\EmailAction;
use App\Directives\BaseDirective;

class HotsEmail extends BaseDirective
{
	/**
	 * Attributes for Tatter\Handlers
	 *
	 * @var array<string, mixed>      
	 */
	public $attributes = [
		'name'      => 'Heroes of the Storm Emailer', // Name for this Action
		'uid'       => 'hots_email', // Unique identifier
		'subreddit' => 'hots_email', // The portion after "/r/"
		'rate'      => MINUTE, // How frequently to check
		'action'    => EmailAction::class, // Action to use in responding
		'patterns'  => [ // Regex patterns to match
			'/Heroes.?Profile/i',
			'/Heroes.?Share/',
			'/Hots.?Api/i',
			'/Hots.?Logs/i',
		],
	];
}
