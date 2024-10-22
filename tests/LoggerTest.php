<?php

namespace Logger\Tests;

require_once realpath(__DIR__ . "/../") . '/src/Logger.php';

use PHPUnit\Framework\TestCase;
use CorianderCore\Modules\Logger;
use DateTimeZone;
use Exception;
use ReflectionClass;

/**
 * Class LoggerTest
 *
 * This test class verifies the functionality of the Logger class.
 * It includes tests for log file creation, time zone handling,
 * and reset interval functionality.
 */
class LoggerTest extends TestCase
{
    /**
     * @var string Path to the temporary log file for testing.
     */
    private static $logFilePath;

    /**
     * setUpBeforeClass
     *
     * This method runs once before all tests in the class. It sets up the log file path.
     */
    public static function setUpBeforeClass(): void
    {
        // Define the path to the temporary log file
        self::$logFilePath = sys_get_temp_dir() . '/test_log.log';
    }

    /**
     * tearDownAfterClass
     *
     * This method runs once after all tests in the class are completed.
     * It cleans up the test environment by removing test files.
     */
    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$logFilePath)) {
            unlink(self::$logFilePath); // Clean up the log file after tests
        }
    }

    /**
     * Uses reflection to access a private or protected property.
     *
     * @param object $object The object instance.
     * @param string $property The property name.
     * @return mixed The value of the property.
     */
    private function getPrivateProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * testLoggerConstructor
     *
     * Tests that the Logger constructor initializes correctly and throws an exception for invalid interval units.
     */
    public function testLoggerConstructor()
    {
        $logger = new Logger(self::$logFilePath, 'Europe/Paris', 'days', 1);
        $this->assertInstanceOf(Logger::class, $logger);

        // Access the private $timeZone property using reflection
        $timeZone = $this->getPrivateProperty($logger, 'timeZone');
        $this->assertEquals(new DateTimeZone('Europe/Paris'), $timeZone);

        // Test invalid interval unit exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid interval unit. Allowed units are: 'hours', 'days', 'weeks'.");
        new Logger(self::$logFilePath, 'Europe/Paris', 'invalid_unit', 1);
    }

    /**
     * testLogFileCreation
     *
     * Tests that the log file is created and that a message is logged correctly.
     */
    public function testLogFileCreation()
    {
        $logger = new Logger(self::$logFilePath, 'Europe/Paris', 'days', 1);

        // Log a message
        $logger->log('TestFile.php', 'info', 'This is a test log message.');

        // Assert that the log file is created
        $this->assertFileExists(self::$logFilePath, 'Log file was not created.');

        // Verify the content of the log file
        $logContent = file_get_contents(self::$logFilePath);
        $this->assertStringContainsString('This is a test log message.', $logContent, 'Log message was not written correctly.');
    }

    /**
     * testResetLogFileBasedOnInterval
     *
     * Tests the log file reset functionality based on the reset interval.
     */
    public function testResetLogFileBasedOnInterval()
    {
        // Simulate an old log file by modifying its modification time
        file_put_contents(self::$logFilePath, 'Old log content');
        touch(self::$logFilePath, strtotime('-3 days')); // Modify the log file to be 3 days old

        // Create a Logger instance with a reset interval of 1 day
        new Logger(self::$logFilePath, 'Europe/Paris', 'days', 1);

        // Assert that the log file was reset (file should no longer exist)
        $this->assertFileDoesNotExist(self::$logFilePath, 'Log file was not reset based on the interval.');
    }

    /**
     * testNonWritableDirectoryThrowsException
     *
     * Tests that an exception is thrown if the log directory is not writable.
     */
    public function testNonWritableDirectoryThrowsException()
    {
        // Use a non-writable directory
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Log directory is not writable.");
        $logger = new Logger('/non_writable_dir/test_log.log', 'Europe/Paris', 'days', 1);
        $logger->log('TestFile.php', 'error', 'Test log');
    }
}
