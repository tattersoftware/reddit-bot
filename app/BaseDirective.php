<?php namespace App;

use Tatter\Handlers\BaseHandler;

/**
 * Directive Abstract Class
 *
 * Provides the template and common method basis
 * for building bot requests and responses.
 */
abstract class BaseDirective extends BaseHandler
{
	/**
	 * Attributes for Tatter\Handlers
	 *
	 * @var array<string, mixed>      
	 */
	protected $attributes;

	/**
	 * Default set of attributes
	 *
	 * @var array<string, mixed>
	 */
	private $defaults = [
		'name'       => '', // Name for this Action
		'uid'        => '', // Unique identifier
		'rate'       => MINUTE, // How frequently to check
		'subreddits' => [], // Array of strings, the portion after "/r/"
		'patterns'   => [], // Regex patterns to match
		'actions'    => [], // Actions to use during execution
		'params'     => [], // Arrays of parameters to pass to each Action
	];

	/**
	 * Merges default attributes with child
	 */
	public function __construct()
	{
		$this->attributes = array_merge($this->defaults, $this->attributes);
	}
}
