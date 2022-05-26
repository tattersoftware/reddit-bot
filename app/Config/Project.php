<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Project extends BaseConfig
{
    /**
     * Path to use for storing Submissions.
     *
     * @var string
     */
    public $submissionsPath = WRITEPATH . 'submissions' . DIRECTORY_SEPARATOR;
}
