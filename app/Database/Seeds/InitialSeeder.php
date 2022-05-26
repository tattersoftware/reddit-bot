<?php namespace App\Database\Seeds;

use Exception;
use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
	public function run()
	{
		$errors = [];

		// Seeds to run
		$seeds = [
			'App\Database\Seeds\TemplateSeeder',
		];

		// Run each seeder in order
		foreach ($seeds as $seedName)
		{
			try
			{
				$this->call($seedName);
			}
			catch (Exception $e)
			{
				// Pass CLI exceptions back to BaseCommand for display
				if (is_cli())
				{
					throw $e;
				}
				$errors[] = $e->getFile() . ' - ' . $e->getLine() . ': ' . $e->getMessage() . " (for {$seedName})";
			}
		}
		
		return $errors;
	}
}
