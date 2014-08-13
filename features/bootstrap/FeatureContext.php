<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Process\Process;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    const TMP_DIR_NAME = 'valkyrie';

    /**
     * @var string
     */
    private $workingDir;

    /**
     * @var Process
     */
    private $process;

    /**
     * Prepares test folders in the temporary directory.
     *
     * @BeforeScenario
     */
    public function prepareTestFolders()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::TMP_DIR_NAME . DIRECTORY_SEPARATOR .
            md5(microtime() * rand(0, 10000));

        $this->workingDir = $dir;
        $this->process = new Process(null);
    }

    /**
     * Cleans test folders in the temporary directory.
     *
     * @BeforeSuite
     * @AfterSuite
     */
    public static function cleanTestFolders()
    {
        if (is_dir($dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::TMP_DIR_NAME)) {
            self::clearDirectory($dir);
        }
    }

    /**
     * @param string $filename
     * @param string $content
     */
    private function createFile($filename, $content)
    {
        $path = dirname($filename);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($filename, $content);
    }

    /**
     * @param string $path
     */
    private static function clearDirectory($path)
    {
        $files = scandir($path);
        array_shift($files);
        array_shift($files);

        foreach ($files as $file) {
            $file = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file)) {
                self::clearDirectory($file);
            } else {
                unlink($file);
            }
        }

        rmdir($path);
    }

    /**
     * @Given /^there is a file containing:$/
     *
     * @param PyStringNode $content
     */
    public function thereIsAFileContaining(PyStringNode $content)
    {
        $this->createFile($this->workingDir . '/endpoint_call.vlk', (string) $content);
    }

    /**
     * @When /^I run "valkyrie"$/
     */
    public function iRunValkyrie()
    {
        $this->process->setWorkingDirectory($this->workingDir);
        $this->process->setCommandLine('valkyrie');
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then /^it should pass$/
     */
    public function itShouldPass()
    {
        expect($this->process->getExitCode())->toBe(0);
    }
}
