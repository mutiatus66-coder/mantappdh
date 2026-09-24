<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
<<<<<<< HEAD
        putenv('DUSK_HEADLESS_DISABLED=1');
        
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
=======
        if (! env('DUSK_HEADLESS_DISABLED', false) && file_exists('/usr/bin/chromedriver')) {
            static::useChromedriver('/usr/bin/chromedriver');
        }

        $driverUrl = $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL');

        if (! static::runningInSail() && ! $driverUrl) {
            // If running inside container where glibc chromedriver binary cannot execute, don't fail hard
            try {
                static::startChromeDriver(['--port=9515']);
            } catch (\Throwable $e) {
                // Fallback to external driver
            }
>>>>>>> 05ae2b9fe0d396e707e2accab1eb16705c2febb0
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--no-sandbox',
            '--disable-dev-shm-usage',
<<<<<<< HEAD
            // Disable headless completely
        ]);
=======
            '--ignore-certificate-errors',
        ];

        if (env('DUSK_HEADLESS', false) && ! env('DUSK_HEADLESS_DISABLED', false)) {
            $arguments[] = '--headless=new';
        }

        $options = (new ChromeOptions)->addArguments($arguments);
>>>>>>> 05ae2b9fe0d396e707e2accab1eb16705c2febb0

        if (! env('DUSK_HEADLESS_DISABLED', false)) {
            if (file_exists('/usr/bin/chromium')) {
                $options->setBinary('/usr/bin/chromium');
            } elseif (file_exists('/usr/bin/chromium-browser')) {
                $options->setBinary('/usr/bin/chromium-browser');
            }
        }

        $driverUrl = $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL');
        if (! $driverUrl) {
            if (env('DUSK_HEADLESS_DISABLED', false) && (file_exists('/.dockerenv') || file_exists('/run/.containerenv'))) {
                $driverUrl = 'http://host.docker.internal:9515';
            } else {
                $driverUrl = 'http://localhost:9515';
            }
        }

        return RemoteWebDriver::create(
            $driverUrl,
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
