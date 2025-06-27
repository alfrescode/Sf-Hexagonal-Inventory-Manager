<?php

use App\Kernel;

if (isset($_ENV['ERROR_REPORTING'])) {
    error_reporting(eval('return ' . $_ENV['ERROR_REPORTING'] . ';'));
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
