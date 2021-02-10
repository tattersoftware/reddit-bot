<?php namespace App\Commands;

use App\BaseDirective;
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
	 * Directives instances from Handlers
	 *
	 * @var BaseDirective[]
	 */
	protected $directives = [];

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

		$this->reddit    = service('Reddit');
		$this->directory = rtrim(config('Project')->submissionsPath, '/') . '/';

		foreach (service('Handlers', 'Directives')->findAll() as $class)
		{
			$this->directives[] = new $class();
		}
	}
}
