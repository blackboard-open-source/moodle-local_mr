<?php
/**
 * Moodlerooms Framework
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @copyright Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @package mr
 * @author Mark Nielsen
 */

/**
 * MR Notify
 *
 * This model is used to add messages to the session
 * which can then be printed on subsequent page loads.
 *
 * Use case: submit data to be saved, set a message with
 * mr_notify like "Changes Saved" and then redirect to the
 * orignal screen and display the message.
 *
 * This class is tightly integrated with mr_controller.
 *
 * @author Mark Nielsen
 * @package mr
 * @see mr_controller
 */
class mr_notify {
    /**
     * Message that is bad
     */
    const BAD = 'notifyproblem';

    /**
     * Message that is good
     */
    const GOOD = 'notifysuccess';

    /**
     * Get string module key
     *
     * @var string
     */
    protected $module = '';

    /**
     * Message alignment
     *
     * @var string
     */
    protected $align  = 'center';

    /**
     * Constructor
     *
     * Override default module and align.
     *
     * @param string $module Default get string module key
     * @param string $align Default alignment
     */
    public function __construct($module = '', $align = 'center') {
        $this->set_module($module)
             ->set_align($align);
    }

    /**
     * Set module string
     *
     * @param string $module Get string module key
     * @return mr_notify
     */
    public function set_module($module) {
        $this->module = $module;
        return $this;
    }

    /**
     * Set alignment
     *
     * @param string $align Alignment
     * @return mr_notify
     */
    public function set_align($align) {
        $this->align = $align;
        return $this;
    }

    /**
     * Add a good message
     *
     * @param string $identifier The string identifier to use in get_string()
     * @param mixed $a To be passed in the call to get_string()
     * @param string $module Get string module key
     * @param string $align Alignment of the message
     * @return mr_notify
     */
    public function good($identifier, $a = NULL, $module = NULL, $align = NULL) {
        return $this->add($identifier, self::GOOD, $a, $module, $align);
    }

    /**
     * Add a bad message
     *
     * @param string $identifier The string identifier to use in get_string()
     * @param mixed $a To be passed in the call to get_string()
     * @param string $module Get string module key
     * @param string $align Alignment of the message
     * @return mr_notify
     */
    public function bad($identifier, $a = NULL, $module = NULL, $align = NULL) {
        return $this->add($identifier, self::BAD, $a, $module, $align);
    }

    /**
     * Adds a message to be printed.  Messages are printed
     * by calling {@link print()}.
     *
     * @uses $SESSION
     * @param string $identifier The string identifier to use in get_string()
     * @param string $class Class to be passed to notify().  Usually notifyproblem or notifysuccess.
     * @param mixed $a To be passed in the call to get_string()
     * @param string $module Get string module key
     * @param string $align Alignment of the message
     * @example controller/default.php See this being used in a mr_controller
     * @return mr_notify
     * @see BAD, GOOD
     */
    public function add($identifier, $class = self::BAD, $a = NULL, $module = NULL, $align = NULL) {
        if (is_null($module)) {
            $module = $this->module;
        }

        return $this->add_string(get_string($identifier, $module, $a), $class, $align);
    }

    /**
     * Add a string to be printed
     *
     * @param string $string The string to be printed
     * @param string $class The class to be passed to notify().  Usually notifyproblem or notifysuccess.
     * @param string $align Alignment of the message
     * @return mr_notify
     * @see BAD, GOOD
     */
    public function add_string($string, $class = self::BAD, $align = NULL) {
        global $SESSION;

        if (empty($SESSION->messages) or !is_array($SESSION->messages)) {
            $SESSION->messages = array();
        }
        if (is_null($align)) {
            $align = $this->align;
        }
        $SESSION->messages[] = array($string, $class, $align);

        return $this;
    }

    /**
     * Display all messages added to the session.
     *
     * @uses $SESSION
     * @return string
     */
    public function display() {
        global $SESSION;

        $output = '';

        if (!empty($SESSION->messages)) {
            foreach($SESSION->messages as $message) {
                $output .= notify($message[0], $message[1], $message[2], true);
            }
        }
        // Reset
        unset($SESSION->messages);

        return $output;
    }
}