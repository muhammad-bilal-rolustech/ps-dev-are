<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

/**
 *
 * Composer configuration validation and merge logic. This will be moved into
 * HealthCheck in the future once it is enable for sugar 7 upgrades.
 *
 */
class SugarUpgradeCheckComposerConfig extends UpgradeScript
{
    /**
     * @var string composer.json file name
     */
    const COMPOSER_JSON = 'composer.json';

    /**
     * @var string composer.lock file name
     */
    const COMPOSER_LOCK = 'composer.lock';

    /**
     * {@inheritDoc}
     */
    public $order = 200;

    /**
     * {@inheritDoc}
     */
    public $version = '7.6.0';

    /**
     * {@inheritDoc}
     * Does not run on db-only updates
     */
    public $type = self::UPGRADE_CORE;

    /**
     * Path to composer.json
     * @var string
     */
    protected $jsonFile = '';

    /**
     * Path to new composer.json
     * @var string
     */
    protected $newJsonFile = '';

    /**
     * Path to composer.lock
     * @var string
     */
    protected $lockFile = '';

    /**
     * Composer lock hash
     * @var string
     */
    protected $lockHash = '';

    /**
     * Installed packages from lock file
     * @var array
     */
    protected $lockPackages = array();

    /**
     * List of registered repositories from lock file
     * @var unknown
     */
    protected $lockRepos = array();

    /**
     * List of shipped composer hashes
     * @var array
     */
    protected $shipped = array(
        '7.5.0.0' => '72d3be63d2481cdb3710f86f8a36b851',
        '7.5.0.1' => '72d3be63d2481cdb3710f86f8a36b851',
        '7.5.1.0' => '72d3be63d2481cdb3710f86f8a36b851',
        '7.5.2.0' => '72d3be63d2481cdb3710f86f8a36b851',
        '7.5.2.1' => '72d3be63d2481cdb3710f86f8a36b851',
        '7.5.2.2' => '72d3be63d2481cdb3710f86f8a36b851',
        '7.5.2.3' => '72d3be63d2481cdb3710f86f8a36b851',
        '7.6.0.0' => 'fafc925d2c41624ab200143c523659a8',
        '7.6.1.0' => 'af617767509def89c0e1dea33c176a0c',
    );

    /**
     * Target upgrade definition
     * @var array
     */
    protected $target = array();

    /**
     * List of generic settings which are always overruled by SugarCRM
     * @var array
     */
    protected $genericSettings = array(
        'name',
        'description',
        'type',
        'license',
        'homepage',
        'support',
        'minimum-stability',
        'autoload', // We may need to expand this later on
        'config',   // We may need to expand this later on
    );

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (!$this->initialize()) {
            return $this->error("Composer configuration initialization error");
        }

        if ($this->skipMerge()) {
            return true;
        }

        $this->log("Custom composer configuration detected");
        $this->loadTargetDefinition();

        // Determine missing packages/repositories.
        $missingPack = $this->getMissingPackages($this->target['packages'], $this->lockPackages);
        $missingRepo = $this->getMissingRepos($this->target['repositories'], $this->lockRepos);

        // Vaiidate generic settings
        $validsettings = $this->validateGenericSettings($this->target, $this->loadFromFile($this->jsonFile));

        // If all packages and repos are satisfied we can still continue the upgrade.
        if ($validsettings && empty($missingPack) && empty($missingRepo)) {
            $this->log("Custom composer configuration is valid for upgrade");
            $this->useCustomComposerFiles(array($this->jsonFile, $this->lockFile));
            return true;
        }

        /*
         * If we reach this point, we will bail out and log the changes the
         * administrator needs to perform on composer before running the
         * upgrade. The least we can do is propose an updated version of
         * composer.json. However its up to the administrator to solve this
         * merge issues manually as it requires the execution of composer
         * commands which we do not perform directly from within sugar code.
         */

        // Create a proposal file as a guideline.
        $this->createProposal(
            $this->loadFromFile($this->jsonFile),
            $this->target['generic'],
            $missingPack,
            $missingRepo
        );

        // Generate user error
        $error = "A custom composer configuration has been detected which is incompatible ";
        $error .= "with the upgrade process. Consult the SugarCRM Administration Guide for ";
        $error .= "more details on how to resolve this issue. Detailed logs are available in %s.";

        return $this->error(sprintf($error, $this->context['log']), true);
    }

    /**
     * Validate generic settings. For now we require the generic settings to
     * be current with the settings of the shipped composer.json file. When
     * we implement an active approach, the generic settings can be silently
     * updated as they have no direct influence on composer's functionality.
     *
     * @return boolean
     */
    protected function validateGenericSettings(array $target, array $config)
    {
        $valid = true;

        foreach ($target['generic'] as $key => $value) {
            if (!isset($config[$key])) {
                $valid = false;
                $this->log("Missing configuration key '$key'");
            } elseif ($config[$key] !== $value) {
                $valid = false;
                $this->log("Wrong value for configuration key '$key'");
            }
        }

        return $valid;
    }

    /**
     * Create a proposal for composer.json. This is only meant to be used as
     * a guideline for the administrator to update the custom composer.json
     * file. Its the administrators responsability to validate it and update
     * composer accordingly to satisfy the upgrade process.
     *
     * @param array $config Current configuration to amend
     * @param array $generic List of generic configuration
     * @param array $missingPack List of missing packages
     * @param array $missingRepo List of missing repositories
     */
    protected function createProposal(array $config, array $generic, array $missingPack, array $missingRepo)
    {
        $file = $this->newJsonFile . '.proposal';
        $this->log("Generating proposal file $file");

        // Set immutable generic configuration
        foreach ($generic as $key => $value) {
            $config[$key] = $value;
        }

        // Add mssing packages.
        foreach ($missingPack as $pack => $version) {
            $config['require'][$pack] = $version;
        }

        // Add missing repositories.
        foreach ($missingRepo as $repo => $type) {
            $config['repositories'][] = array(
                'url' => $repo,
                'type' => $type,
            );
        }

        $this->saveToFile($file, $config);
    }

    /**
     * Backup the current source composer files so the upgrade post step can
     * move them back in place.
     *
     * @param array $files List of file to park for post process
     */
    protected function useCustomComposerFiles(array $files)
    {
        foreach ($files as $file) {
            $this->copy($file, $file . '.valid');
        }

        // Pass the files into the upgrader state so post step can pick them up.
        $this->upgrader->state['composer_custom'] = $files;
    }

    /**
     * Initialization
     */
    protected function initialize()
    {
        if (empty($this->context['source_dir'])) {
            $this->log("No source_dir context available");
            return false;
        }

        if (empty($this->context['new_source_dir'])) {
            $this->log("No new_source_dir context available");
            return false;
        }

        // Setup file locations.
        $this->jsonFile = $this->context['source_dir'] . '/' . self::COMPOSER_JSON;
        $this->lockFile = $this->context['source_dir'] . '/' . self::COMPOSER_LOCK;
        $this->newJsonFile = $this->context['new_source_dir'] . '/' . self::COMPOSER_JSON;

        $this->log("Using {$this->jsonFile} as composer.json source");
        $this->log("Using {$this->lockFile} as composer.lock source");
        $this->log("Using {$this->newJsonFile} as composer.json target");

        return true;
    }

    /**
     * Get list of missing packages
     * @param array $target List of target packages
     * @param array $lock Composer lock packages
     * @return array
     */
    protected function getMissingPackages(array $target, array $lock)
    {
        $callable = array($this, 'isPackageAvailable');
        return $this->getMissing($callable, $target, $lock);
    }

    /**
     * Get list of missing repositories
     * @param array $target List of target repositories
     * @param array $lock Composer lock repositories
     * @return array
     */
    protected function getMissingRepos(array $target, array $lock)
    {
        $callable = array($this, 'isRepoAvailable');
        return $this->getMissing($callable, $target, $lock);
    }

    /**
     * Get list of missing items based on callable
     * @param callable $callable
     * @param array $target List of target packages
     * @param array $lock Composer lock content
     * @return array
     */
    protected function getMissing($callable, array $target, array $lock)
    {
        $missing = array();
        foreach ($target as $key => $value) {
            if (!call_user_func($callable, $key, $value, $lock)) {
                $missing[$key] = $value;
            }
        }
        return $missing;
    }

    /**
     * Load target definition from new composer.json.
     * @return array
     */
    protected function loadTargetDefinition()
    {
        // Initialize target.
        $this->target = array(
            'generic' => array(),
            'packages' => array(),
            'repositories' => array(),
        );

        $new = $this->loadFromFile($this->newJsonFile);

        // Parse generic settings
        foreach ($this->genericSettings as $key) {
            if (isset($new[$key])) {
                $this->target['generic'][$key] = $new[$key];
            }
        }

        // Parse required packages (we do not really care about dev packages).
        foreach ($new['require'] as $package => $version) {
            $this->target['packages'][$package] = $version;
        }

        // Parse repositories.
        if (isset($new['repositories'])) {
            foreach ($new['repositories'] as $repo) {
                $url = $repo['url'];
                $type = $repo['type'];
                $this->target['repositories'][$url] = $type;
            }
        }

        return $this->target;
    }

    /**
     * To determine whether we are dealing with a stock composer file we
     * compare the composer.json hash with the one reported in composer.lock
     * and the one we know from our shipped releases. If they do not match
     * up someone made a customization somewhere.
     *
     * @return boolean
     */
    protected function isStockComposer()
    {
        // Load lock file into memory.
        $this->loadLock();

        if (empty($this->lockHash)) {
            $this->log("No hash available in lock file");
            return false;
        }

        // Compare hash from lock file against composer.json content
        $md5 = md5_file($this->jsonFile);
        if ($md5 !== $this->lockHash) {
            $this->log("Composer lock not up to date with json file");
            return false;
        }

        // Check if shipped hash is available
        if (!isset($this->shipped[$this->from_version])) {
            $this->log("No release hash available for {$this->from_version}");
            return false;
        }

        // Check if hash matches up with our previous release.
        if ($md5 !== $this->shipped[$this->from_version]) {
            $this->log("Hash $md5 does not match release hash for {$this->from_version}");
            return false;
        }

        return true;
    }

    /**
     * Load lock file content.
     */
    protected function loadLock()
    {
        $lock = $this->loadFromFile($this->lockFile);

        // Set hash
        $this->lockHash = isset($lock['hash']) ? $lock['hash'] : '';

        // Parse packages and repositories.
        if (isset($lock['packages']) && is_array($lock['packages'])) {
            foreach ($lock['packages'] as $package) {
                $this->lockPackages[$package['name']] = $package['version'];
                $this->lockRepos[$package['source']['url']] = $package['source']['type'];
            }
        }
    }

    /**
     * Verify if given package constraints are available
     * @param string $package Package name
     * @param string $version Version string
     * @param array $lock Composer lock packages
     * @return boolean
     */
    protected function isPackageAvailable($package, $version, array $lock)
    {
        // Skip platform packages for now. We may need to review this once
        // composer is being used actively to define the dependencies.
        if ($this->isPlatformPackage($package)) {
            $this->log("Skipping platform package $package");
            return true;
        }

        // Check if package is known.
        if (!isset($lock[$package])) {
            $this->log("Package $package with version constraint $version is missing");
            return false;
        }

        // Validate version.
        if ($lock[$package] !== $version) {
            $this->log("Package $package has wrong verion {$lock[$package]} ($version required)");
            return false;
        }

        $this->log("Found valid package $package with version constraint $version");
        return true;
    }

    /**
     * Check if given package name is a virtual platform package.
     * @param string $package
     * @return boolean
     */
    protected function isPlatformPackage($package)
    {
        return (bool) !strpos($package, '/');
    }

    /**
     * Verify if given repository is avaiable
     * @param string $repo Repository url
     * @param string $type Repository type
     * @param array $lock Composer lock repositories
     * @return boolean
     */
    protected function isRepoAvailable($repo, $type, array $lock)
    {
        // Check if repository url is known.
        if (!isset($lock[$repo])) {
            $this->log("Repository $repo of type $type is missing");
            return false;
        }

        // Check if type matches.
        if ($lock[$repo] !== $type) {
            $this->log("Repository $repo defined with invalid type {$lock[$repo]}");
            return false;
        }

        $this->log("Found valid repository $repo with type $type");
        return true;
    }


    /**
     * Load JSON from file into an array.
     * @param string $file File name
     * @return array
     */
    protected function loadFromFile($file)
    {
        $json = $this->fileGetContents($file);
        if ($json === false) {
            $this->log("Cannot read $file");
            return array();
        }
        $content = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log("JSON decode error '" . json_last_error_msg() . "' for $file");
            return array();
        }

        return $content;
    }

    /**
     * Save array in JSON format to disk
     * @param string $file File name
     * @param array $content
     * @return boolean
     */
    protected function saveToFile($file, array $content)
    {
        $mask = version_compare(PHP_VERSION, '5.4.0', '>=') ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES: 0;
        $json = json_encode($content, $mask);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log("JSON encode error '" . json_last_error_msg() . "' for $file");
            return false;
        }
        $this->log("Saving file $file to disk");
        return $this->filePutContents($file, $json);

    }

    /**
     * Determine whether we need to merge composer.json.
     * @return boolean
     */
    protected function skipMerge()
    {
        // Composer has been publically introduced since 7.5 so there
        // should not be any composer.json file present in the root.
        if (version_compare($this->from_version, '7.5', '<')) {
            $this->log("Skipping merge, pre 7.5 version");
            return true;
        }

        // In case for one reason composer.json or composer.lock have
        // dissappeared, we skip the merge as there is no reference
        // to start from..
        if ($this->areFilesMissing(array($this->jsonFile, $this->lockFile))) {
            return true;
        }

        // Determine if any changes have been made to composer settings
        if ($this->isStockComposer()) {
            $this->log("Skipping merge, stock composer settings detected");
            return true;
        }

        return false;
    }

    /**
     * Check if files are missing.
     * @param array $files List of files to check
     * @return boolean
     */
    protected function areFilesMissing(array $files)
    {
        $missing = false;
        foreach ($files as $file) {
            if (!file_exists($file)) {
                $missing = true;
                $this->log("Skipping merge, $file missing");
            }
        }
        return $missing;
    }

    /**
     * Get file contents. Proxy to better facilitate unit testing.
     * @param string $file File name
     * @return string
     */
    protected function fileGetContents($file)
    {
        return file_get_contents($file);
    }

    /**
     * Put file contents. Proxy to better facilitate unit testing.
     * @param string $file File name
     * @param string $content
     * @return boolean
     */
    protected function filePutContents($file, $content)
    {
        return (bool) file_put_contents($file, $content);
    }

    /**
     * Copy file. Proxy to better facilitate unit testing.
     * @param string $source
     * @param string $target
     */
    protected function copy($source, $target)
    {
        return copy($source, $target);
    }

}
