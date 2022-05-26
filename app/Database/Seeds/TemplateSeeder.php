<?php

namespace App\Database\Seeds;

use Tatter\Outbox\Database\Seeds\TemplateSeeder as BaseSeeder;
use Tatter\Outbox\Models\TemplateModel;

/**
 * Email Template Seeder
 *
 * Jump-starts Email Templates with some initial
 * values.
 * Methodology: Starts with Outbox's seeder to
 * ensure a Default template is always available
 * (though this could be replaced with any template
 * named "Default"). Each defined Template will be
 * created as a child of Default.
 *
 * Note: Templates should *not* include inline CSS,
 * this is added later by Tatter\Outbox.
 */
class TemplateSeeder extends BaseSeeder
{
    public function run()
    {
        // Run the module version first to ensure Default exists
        parent::run();

        // Use "Default" as the parent (will throw if it does not exist)
        $default = model(TemplateModel::class)->findByName('Default');

        // Define each Template
        $templates = [
            [
                'name'    => 'Reddit Mention',
                'subject' => 'Reddit keyword matched in {kind} {name}',
                'body'    => view('emails/reddit_mention'),
            ],
        ];

        foreach ($templates as $row) {
            if (model(TemplateModel::class)->where('name', $row['name'])->first()) {
                continue;
            }

            // Set the parent
            $row['parent_id'] = $default->id;
            model(TemplateModel::class)->insert($row);
        }
    }
}
