<?php

namespace App\Commands;

use App\Models\SubmissionModel;
use CodeIgniter\CLI\CLI;
use Tatter\Reddit\Structures\Kind;
use Throwable;

/**
 * Reddit Process Task
 *
 * Reads in cached submissions and filters them based
 * on the configured criteria, notifying for matches
 * and removing cached files when done.
 */
class RedditFilter extends RedditCommand
{
    protected $group       = 'Tasks';
    protected $name        = 'reddit:filter';
    protected $description = 'Filter stored Reddit comments and posts.';
    protected $usage       = 'reddit:filter';

    public function run(array $params = [])
    {
        helper(['file', 'text']);

        foreach (get_filenames(config('Project')->submissionsPath, true) as $file) {
            if (basename($file) === 'index.html') {
                continue;
            }

            // Read in the file contents and attempt to unserialize it
            $contents = file_get_contents($file);

            try {
                /** @var Kind $kind */
                $kind = unserialize($contents);
            } catch (Throwable $e) {
                CLI::write('Error processing ' . basename($file) . ': ' . $e->getMessage());

                continue;
            }

            // Convert the Kind to a Submission row
            $row = model(SubmissionModel::class)->fromKind($kind);

            // Include titles of Links
            $search = $row['kind'] === 'Link' ? $row['title'] . ' ' . $row['body'] : $row['body'];

            // Remove newlines to improve pattern matching
            $search = trim(preg_replace('/\s+/', ' ', $search));

            // Check each Directive for a match
            foreach ($this->directives as $directive) {
                // Make sure this is a subreddit for this Directive
                if (! in_array($row['subreddit'], $directive->subreddits, true)) {
                    continue;
                }

                // Check each pattern individually so we can highlight the first match
                foreach ($directive->patterns as $pattern) {
                    if (preg_match($pattern, $search, $matches)) {
                        // Gather the excerpt
                        $row['directive'] = $directive->uid;
                        $row['match']     = $matches[0];
                        $row['excerpt']   = excerpt($search, $row['match']);

                        // Print the header and highlighted version
                        CLI::write($row['kind'] . ' ' . $row['name'] . ' ' . $row['title'], 'green'); // @phpstan-ignore-line
                        CLI::write(highlight_phrase($row['excerpt'], $row['match'], "\033[0;33m", "\033[0m"));

                        // Insert it into the database
                        model(SubmissionModel::class)->insert($row);

                        // Skip to the next Directive
                        continue 2;
                    }
                }
            }

            // Remove the file so it is not processed again
            unlink($file);
        }
    }
}
