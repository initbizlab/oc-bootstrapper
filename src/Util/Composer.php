<?php

namespace OFFLINE\Bootstrapper\October\Util;


use InvalidArgumentException;
use LogicException;
use RuntimeException;

/**
 * Class Composer
 * @package OFFLINE\Bootstrapper\October\Util
 */
class Composer
{
    use RunsProcess;

    /**
     * @var string
     */
    protected $composer;

    /**
     * Run Composer commands.
     */
    public function __construct()
    {
        $this->composer = $this->findComposer();
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd() . DS . 'composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }

    /**
     * Composer install
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws LogicException
     */
    public function install()
    {
        $this->runProcess(
            $this->composer . ' install --no-scripts --no-interaction --prefer-dist',
            'Failed to run composer install',
            3600
        );
    }

    /**
     * Composer update --lock
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws LogicException
     */
    public function updateLock()
    {
        $this->runProcess(
            $this->composer . ' update --no-scripts --no-interaction --prefer-dist --lock',
            'Failed to run composer update',
            3600
        );
    }

    /**
     * Composer require (if not already there)
     *
     * @return void
     * @throws LogicException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function addDependency($package)
    {
        // If the package is already installed don't add it again
        $slashed = str_replace('/', '\/', $package);
        if (preg_grep('/' . $slashed . '/', file(getcwd() . DS . 'composer.json'))) {
            return true;
        }

        $package = escapeshellarg($package);

        $this->runProcess(
            $this->composer . ' require ' . $package . ' --no-interaction',
            'Failed to add composer dependency',
            3600
        );
    }

    /**
     * Composer require <package> <version>
     *
     * @return void
     * @throws LogicException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function requireVersion($package, $version)
    {
        $package = escapeshellarg($package);
        $version = escapeshellarg($version);

        $this->runProcess(
            $this->composer . ' require ' . $package . ' ' . $version . ' --no-interaction',
            'Failed to add  specific composer dependency',
            3600
        );
    }
}