<?php
/**
 * FuzeWorks Tracy Component.
 *
 * The FuzeWorks PHP FrameWork
 *
 * Copyright (C) 2013-2019 TechFuze
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author    TechFuze
 * @copyright Copyright (c) 2013 - 2019, TechFuze. (http://techfuze.net)
 * @license   https://opensource.org/licenses/MIT MIT License
 *
 * @link  http://techfuze.net/fuzeworks
 * @since Version 1.2.0
 *
 * @version Version 1.2.0
 */

namespace FuzeWorks;
use FuzeWorks\Exception\EventException;
use FuzeWorks\Exception\InvalidArgumentException;
use Tracy\Debugger;

/**
 * TracyComponent Class.
 *
 * This class provides a bridge between FuzeWorks\Logger and Tracy Debugging tool.
 *
 * This class enables Tracy and disables the FuzeWorks\Logger.
 * FuzeWorks Logger then redirects its logs to the Tracy Bar. This allows the user to still see FuzeWorks' logs.
 *
 * @author    TechFuze <contact@techfuze.net>
 * @copyright Copyright (c) 2013 - 2019, TechFuze. (http://techfuze.net)
 */
class TracyComponent implements iComponent
{

    /**
     * Whether Tracy is enabled or not
     *
     * @var bool $enableTracy
     */
    protected static $enableTracy = true;

    /**
     * Set to true after tracy has been started
     *
     * @var bool $enabled
     */
    protected static $enabled = false;

    public function getName(): string
    {
        return 'TracyComponent';
    }

    public function getClasses(): array
    {
        return [];
    }

    public function onAddComponent(Configurator $configurator){}

    /**
     * Enables Tracy when requested to do so. Disables FuzeWorks Logger.
     *
     * @param Factory $container
     * @throws EventException
     */
    public function onCreateContainer(Factory $container)
    {
        // If Tracy should not be enabled, escape
        if (self::$enableTracy == false)
        {
            Logger::logInfo("TracyComponent added but not enabled.");
            return;
        }

        // Enable Tracy. Use DEVELOPMENT mode when logger is enabled
        $debuggerEnabled = $container->logger->isEnabled();
        if ($debuggerEnabled)
            Debugger::enable(Debugger::DEVELOPMENT, realpath(Core::$logDir));
        else
            Debugger::enable(Debugger::PRODUCTION, realpath(Core::$logDir));

        // Disable FuzeWorks Logger
        Logger::disableScreenLog();

        // Reset exception handlers
        set_error_handler(array('\FuzeWorks\Core', 'errorHandler'), E_ALL);
        set_exception_handler(array('\FuzeWorks\Core', 'exceptionHandler'));

        // Register exception handler
        Core::addErrorHandler(['\Tracy\Debugger', 'errorHandler'], Priority::LOW);

        // Tracy has an annoying default error 500 page.
        // This page will be suppressed when in production mode.
        if ($debuggerEnabled)
        {
            Core::addExceptionHandler(['\Tracy\Debugger', 'exceptionHandler'], Priority::LOW);
        }
        else
            Core::addExceptionHandler([$this, 'exceptionHandler'], Priority::LOW);

        // Enable bridges
        GitTracyBridge::register();
        LoggerTracyBridge::register();
        self::$enabled = true;
    }

    public function exceptionHandler($exception, $exit = true)
    {
        Debugger::getLogger()->log($exception, \Tracy\Logger::EXCEPTION);
    }

    /**
     * Calls a static method in the Debugger class
     *
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __call($method, $arguments)
    {
        if (!method_exists('\Tracy\Debugger', $method))
            throw new InvalidArgumentException("Could not invoke call on Tracy\Debugger. Method '".$method."' does not exist.");

        return call_user_func_array(['\Tracy\Debugger', $method], $arguments);
    }

    /**
     * Gets a property from the Debugger class
     *
     * @param $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if (!property_exists('\Tracy\Debugger', $name))
            throw new InvalidArgumentException("Could not get property of Tracy\Debugger. Property does not exist.");

        return Debugger::$$name;
    }

    /**
     * Sets a property in the Debugger class
     *
     * @param $name
     * @param $value
     * @throws InvalidArgumentException
     */
    public function __set($name, $value)
    {
        if (!property_exists('\Tracy\Debugger', $name))
            throw new InvalidArgumentException("Could not get property of Tracy\Debugger. Property does not exist.");

        Debugger::$$name = $value;
    }

    /**
     * Enable Tracy
     *
     * Has no effect after container is created
     */
    public static function enableTracy()
    {
        self::$enableTracy = true;
    }

    /**
     * Disable Tracy
     *
     * Has no effect after container is created
     */
    public static function disableTracy()
    {
        self::$enableTracy = false;
    }

    /**
     * Check whether Tracy will be enabled or not
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return self::$enabled;
    }
}