<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\Commands;
use Psr\Log\LoggerInterface;
use Tatter\Handlers\Handlers;
use Tatter\Reddit\Reddit;

/**
 * Reddit Command
 *
 * Base class for initializing common
 * components for Reddit commands.
 */
abstract class RedditCommand extends BaseCommand
{
	/**
	 * The group the command is lumped under
	 * when listing commands.
	 *
	 * @var string
	 */
	protected $group = 'Tasks';

	/**
	 * Reddit API client
	 *
	 * @var Reddit
	 */
	protected $reddit;

	/**
	 * Handler instance for Directives
	 *
	 * @var Handlers
	 */
	protected $directives;

	/**
	 * Directory for storing submissions
	 *
	 * @var string
	 */
	protected $directory;

	/**
	 * BaseCommand constructor.
	 *
	 * @param LoggerInterface $logger
	 * @param Commands        $commands
	 */
	public function __construct(LoggerInterface $logger, Commands $commands)
	{
		parent::__construct($logger, $commands);

		$this->reddit     = service('Reddit');
		$this->directives = service('Handlers', 'Directives');
		$this->directory  = rtrim(config('Project')->submissionsPath, '/') . '/';
	}
}
