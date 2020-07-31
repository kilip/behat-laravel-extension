<?php

/*
 * This file is part of the Behat\LaravelExtension project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Behat test suite context.
 */
class FeatureContext implements Context
{
    /**
     * @var string
     */
    private $phpBin;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $workingDir;

    /**
     * @var string
     */
    private $options = '--format-settings=\'{"timer": false}\' --no-interaction';

    /**
     * @var array
     */
    private $env = [];

    /**
     * @var string
     */
    private $answerString;

    /**
     * Cleans test folders in the temporary directory.
     *
     * @BeforeSuite
     * @AfterSuite
     */
    public static function cleanTestFolders()
    {
        if (is_dir($dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'behat')) {
            self::clearDirectory($dir);
        }
    }

    /**
     * Prepares test folders in the temporary directory.
     *
     * @BeforeScenario
     */
    public function prepareTestFolders()
    {
        $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'behat'.DIRECTORY_SEPARATOR.
            md5(microtime().random_int(0, 10000));

        $phpFinder = new PhpExecutableFinder();
        if (false === $php = $phpFinder->find()) {
            throw new \RuntimeException('Unable to find the PHP executable.');
        }
        $this->workingDir = $dir;
        $this->phpBin = $php;
    }

    /**
     * @AfterScenario
     */
    public function storeLog()
    {
        $logFile = $this->workingDir.'/storage/logs/laravel.log';
        if (!is_file($logFile)) {
            return;
        }

        $target = __DIR__.'/../../build/laravel-logs';
        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }
        copy($logFile, $target.'/'.basename($this->workingDir).'.log');
    }

    /**
     * @Given I am in laravel project directory
     */
    public function iAmInLaravelProjectDir()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $origin = __DIR__.'/../../vendor/laravel/laravel';
        $fs->mirror($origin, $this->workingDir);

        /*
        $contents = sprintf(<<<'CON'

$loader = require '%s';
$loader->addPsr4('App\\', __DIR__ . '/../app/');
$loader->addPsr4('Tests\\', __DIR__ . '/../tests/');

CON
        , realpath(__DIR__.'/../../vendor/autoload.php'));
        $this->createFile($this->workingDir.'/vendor/autoload.php', "<?php\n\n".$contents);
        */

        //$target = $this->workingDir.'/bootstrap/app.php';
        //$appContents = file_get_contents($target);

        //$contents .= '$app = new Illuminate\Foundation\Application(';
        //$contents = str_replace('$app = new Illuminate\Foundation\Application(',$contents,$appContents);

        //$this->createFile($target, $contents);

        $contents = <<<EOC
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:DubKoZEmQ/8MjJgoDJbDQjjnvi63Eeyj4I+lH62I4KM=
APP_DEBUG=true
APP_URL=http://localhost
EOC;

        $this->createFile($this->workingDir.'/.env', $contents);

        return;
    }

    /**
     * Creates a file with specified name and context in current workdir.
     *
     * @Given /^(?:there is )?a file named "([^"]*)" with:$/
     *
     * @param string       $filename name of the file (relative path)
     * @param PyStringNode $content  PyString string instance
     */
    public function aFileNamedWith($filename, PyStringNode $content)
    {
        $content = (string) $content;
        $content = strtr($content, ["'''" => '"""']);
        $this->createFile($this->workingDir.'/'.$filename, $content);
    }

    /**
     * Creates a empty file with specified name in current workdir.
     *
     * @Given /^(?:there is )?a file named "([^"]*)"$/
     *
     * @param string $filename name of the file (relative path)
     */
    public function aFileNamed($filename)
    {
        $this->createFile($this->workingDir.'/'.$filename, '');
    }

    /**
     * Creates a noop feature context in current workdir.
     *
     * @Given /^(?:there is )?a some feature context$/
     */
    public function aNoopFeatureContext()
    {
        $filename = 'features/bootstrap/FeatureContext.php';
        $content = <<<'EOL'
<?php

use Behat\Behat\Context\Context;

class FeatureContext implements Context
{
}
EOL;
        $this->createFile($this->workingDir.'/'.$filename, $content);
    }

    /**
     * Creates a noop feature in current workdir.
     *
     * @Given /^(?:there is )?a some feature scenarios/
     */
    public function aNoopFeature()
    {
        $filename = 'features/bootstrap/FeatureContext.php';
        $content = <<<'EOL'
Feature:
        Scenario:
          When this scenario executes
EOL;
        $this->createFile($this->workingDir.'/'.$filename, $content);
    }

    /**
     * Moves user to the specified path.
     *
     * @Given /^I am in the "([^"]*)" path$/
     *
     * @param string $path
     */
    public function iAmInThePath($path)
    {
        $this->moveToNewPath($path);
    }

    /**
     * Checks whether a file at provided path exists.
     *
     * @Given /^file "([^"]*)" should exist$/
     *
     * @param string $path
     */
    public function fileShouldExist($path)
    {
        Assert::assertFileExists($this->workingDir.DIRECTORY_SEPARATOR.$path);
    }

    /**
     * Sets specified ENV variable.
     *
     * @When /^"BEHAT_PARAMS" environment variable is set to:$/
     *
     * @param PyStringNode $value
     */
    public function iSetEnvironmentVariable(PyStringNode $value)
    {
        $this->env = ['BEHAT_PARAMS' => (string) $value];
    }

    /**
     * Runs behat command with provided parameters.
     *
     * @When /^I run "behat(?: ((?:\"|[^"])*))?"$/
     *
     * @param string $argumentsString
     */
    public function iRunBehat($argumentsString = '')
    {
        $argumentsString = strtr($argumentsString, ['\'' => '"']);

        $cmd = sprintf(
            '%s %s %s %s',
            $this->phpBin,
            escapeshellarg(BEHAT_BIN_PATH),
            $argumentsString,
            strtr($this->options, ['\'' => '"', '"' => '\"'])
        );

        if (method_exists('\\Symfony\\Component\\Process\\Process', 'fromShellCommandline')) {
            $this->process = Process::fromShellCommandline($cmd);
        } else {
            // BC layer for symfony/process 4.1 and older
            $this->process = new Process(null);
            $this->process->setCommandLine($cmd);
        }

        // Prepare the process parameters.
        //$this->process->setTimeout(20);
        $this->process->setEnv($this->env);
        $this->process->setWorkingDirectory($this->workingDir);

        if (!empty($this->answerString)) {
            $this->process->setInput($this->answerString);
        }

        // Don't reset the LANG variable on HHVM, because it breaks HHVM itself
        if (!defined('HHVM_VERSION')) {
            $env = $this->process->getEnv();
            $env['LANG'] = 'en'; // Ensures that the default language is en, whatever the OS locale is.
            $this->process->setEnv($env);
        }

        $this->process->run();
    }

    /**
     * Runs behat command with provided parameters in interactive mode.
     *
     * @When /^I answer "([^"]+)" when running "behat(?: ((?:\"|[^"])*))?"$/
     *
     * @param string $answerString
     * @param string $argumentsString
     */
    public function iRunBehatInteractively($answerString, $argumentsString)
    {
        $this->env['SHELL_INTERACTIVE'] = true;

        $this->answerString = $answerString;

        $this->options = '--format-settings=\'{"timer": false}\'';
        $this->iRunBehat($argumentsString);
    }

    /**
     * Runs behat command in debug mode.
     *
     * @When /^I run behat in debug mode$/
     */
    public function iRunBehatInDebugMode()
    {
        $this->options = '';
        $this->iRunBehat('--debug');
    }

    /**
     * Checks whether previously ran command passes|fails with provided output.
     *
     * @Then /^it should (fail|pass) with:$/
     *
     * @param string       $success "fail" or "pass"
     * @param PyStringNode $text    PyString text instance
     */
    public function itShouldPassWith($success, PyStringNode $text)
    {
        $this->itShouldFail($success);
        $this->theOutputShouldContain($text);
    }

    /**
     * Checks whether previously runned command passes|failes with no output.
     *
     * @Then /^it should (fail|pass) with no output$/
     *
     * @param string $success "fail" or "pass"
     */
    public function itShouldPassWithNoOutput($success)
    {
        $this->itShouldFail($success);
        Assert::assertEmpty($this->getOutput());
    }

    /**
     * Checks whether specified file exists and contains specified string.
     *
     * @Then /^"([^"]*)" file should contain:$/
     *
     * @param string       $path file path
     * @param PyStringNode $text file content
     */
    public function fileShouldContain($path, PyStringNode $text)
    {
        $path = $this->workingDir.'/'.$path;
        Assert::assertFileExists($path);

        $fileContent = trim(file_get_contents($path));
        // Normalize the line endings in the output
        if ("\n" !== PHP_EOL) {
            $fileContent = str_replace(PHP_EOL, "\n", $fileContent);
        }

        Assert::assertEquals($this->getExpectedOutput($text), $fileContent);
    }

    /**
     * Checks whether specified content and structure of the xml is correct without worrying about layout.
     *
     * @Then /^"([^"]*)" file xml should be like:$/
     *
     * @param string       $path file path
     * @param PyStringNode $text file content
     */
    public function fileXmlShouldBeLike($path, PyStringNode $text)
    {
        $path = $this->workingDir.'/'.$path;
        Assert::assertFileExists($path);

        $fileContent = trim(file_get_contents($path));

        $fileContent = preg_replace('/time="(.*)"/', 'time="-IGNORE-VALUE-"', $fileContent);

        $dom = new DOMDocument();
        $dom->loadXML($text);
        $dom->formatOutput = true;

        Assert::assertEquals(trim($dom->saveXML(null, LIBXML_NOEMPTYTAG)), $fileContent);
    }

    /**
     * Checks whether last command output contains provided string.
     *
     * @Then the output should contain:
     *
     * @param PyStringNode $text PyString text instance
     */
    public function theOutputShouldContain(PyStringNode $text)
    {
        Assert::assertStringContainsString($this->getExpectedOutput($text), $this->getOutput());
    }

    private function getExpectedOutput(PyStringNode $expectedText)
    {
        $expectedText = (string) $expectedText;
        $text = strtr($expectedText, [
            '\'\'\'' => '"""',
            '%%TMP_DIR%%' => sys_get_temp_dir().DIRECTORY_SEPARATOR,
            '%%WORKING_DIR%%' => realpath($this->workingDir.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR,
            '%%DS%%' => DIRECTORY_SEPARATOR,
        ]);

        // windows path fix
        if ('/' !== DIRECTORY_SEPARATOR) {
            $text = preg_replace_callback(
                '/[ "]features\/[^\n "]+/', function ($matches) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $matches[0]);
                }, $text
            );
            $text = preg_replace_callback(
                '/\<span class\="path"\>features\/[^\<]+/', function ($matches) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $matches[0]);
                }, $text
            );
            $text = preg_replace_callback(
                '/\+[fd] [^ ]+/', function ($matches) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $matches[0]);
                }, $text
            );

            // error stacktrace
            $text = preg_replace_callback(
                '/#\d+ [^:]+:/', function ($matches) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $matches[0]);
                }, $text
            );
        }

        return $text;
    }

    /**
     * Checks whether previously ran command failed|passed.
     *
     * @Then /^it should (fail|pass)$/
     *
     * @param string $success "fail" or "pass"
     */
    public function itShouldFail($success)
    {
        if ('fail' === $success) {
            if (0 === $this->getExitCode()) {
                echo 'Actual output:'.PHP_EOL.PHP_EOL.$this->getOutput();
            }

            Assert::assertNotEquals(0, $this->getExitCode());
        } else {
            if (0 !== $this->getExitCode()) {
                echo 'Actual output:'.PHP_EOL.PHP_EOL.$this->getOutput();
            }

            Assert::assertEquals(0, $this->getExitCode());
        }
    }

    /**
     * Checks whether the file is valid according to an XML schema.
     *
     * @Then /^the file "([^"]+)" should be a valid document according to "([^"]+)"$/
     *
     * @param string $xmlFile
     * @param string $schemaPath relative to features/bootstrap/schema
     */
    public function xmlShouldBeValid($xmlFile, $schemaPath)
    {
        $dom = new DomDocument();
        $dom->load($this->workingDir.'/'.$xmlFile);

        $dom->schemaValidate(__DIR__.'/schema/'.$schemaPath);
    }

    private function getExitCode()
    {
        return $this->process->getExitCode();
    }

    private function getOutput()
    {
        $output = $this->process->getErrorOutput().$this->process->getOutput();

        // Normalize the line endings and directory separators in the output
        if ("\n" !== PHP_EOL) {
            $output = str_replace(PHP_EOL, "\n", $output);
        }

        // Remove location of the project
        $output = str_replace(realpath(dirname(__DIR__, 2)).DIRECTORY_SEPARATOR, '', $output);

        // Replace wrong warning message of HHVM
        $output = str_replace('Notice: Undefined index: ', 'Notice: Undefined offset: ', $output);

        return trim(preg_replace('/ +$/m', '', $output));
    }

    private function createFile($filename, $content)
    {
        $path = dirname($filename);
        $this->createDirectory($path);

        file_put_contents($filename, $content);
    }

    private function createDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function moveToNewPath($path)
    {
        $newWorkingDir = $this->workingDir.'/'.$path;
        if (!file_exists($newWorkingDir)) {
            mkdir($newWorkingDir, 0777, true);
        }

        $this->workingDir = $newWorkingDir;
    }

    private static function clearDirectory($path)
    {
        $files = scandir($path);
        array_shift($files);
        array_shift($files);

        foreach ($files as $file) {
            $file = $path.DIRECTORY_SEPARATOR.$file;
            if (is_dir($file)) {
                self::clearDirectory($file);
            } else {
                unlink($file);
            }
        }

        rmdir($path);
    }
}
