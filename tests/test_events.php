<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests: testing events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests: testing events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_events {

    /**
     * Data generator.
     *
     * @var stdClass $generator
     */
    public $generator;

    /**
     * The user who triggers the events.
     *
     * @var stdClass $user
     */
    public $user;

    /**
     * The course in which the events occur.
     *
     * @var stdClass $course
     */
    public $course;

    /**
     * The course on which the events occur.
     *
     * @var stdClass $module
     */
    public $module;


    /**
     * Constructor.
     * 
     * @param stdClass $user The user who triggers the events.
     * @param stdClass $generator Data generator.
     */
    public function __construct($generator, $user) {
        $this->generator = $generator;
        $this->user = $user;
    }

    /**
     * Get all events.
     * 
     * @return array
     */
    public function all_events() {

        return array_merge(
            $this->authentication_events(),
            $this->navigation_events()
        );
    }

    /**
     * Get authentication events.
     * 
     * @return array
     */
    public function authentication_events() {
        return [
            $this->user_loggedout(),
            $this->user_loggedin(),
        ];
    }

    /**
     * Get navigation events.
     * 
     * @return array
     */
    public function navigation_events() {
        $events = [
            $this->course_viewed(),
        ];
        $types = [
            'book', 'chat', 'choice', 'data', 'feedback', 'folder', 'forum', 'glossary',
            'imscp', 'lesson', 'lti', 'page', 'quiz', 'resource', 'scorm', 'survey', 'url',
            'wiki', 'workshop'
        ];
        foreach ($types as $type) {
            $events[] = $this->course_module_viewed($type);
        }
        return $events;
    }

    /**
     * Get user_loggedin event.
     * 
     * @return \core\event\user_loggedin
     */
    public function user_loggedin() {
        return \core\event\user_loggedin::create([
            'objectid' => $this->user->id,
            'other' => ['username' => $this->user->username],
        ]);
    }

    /**
     * Get user_loggedout event.
     * 
     * @return \core\event\user_loggedout
     */
    public function user_loggedout() {
        return \core\event\user_loggedout::create([
            'objectid' => $this->user->id,
            'other' => ['username' => $this->user->username],
        ]);
    }

    /**
     * Get course_viewed event.
     * 
     * @return \core\event\course_viewed
     */
    public function course_viewed() {
        $this->set_course();
        return \core\event\course_viewed::create([
            'context' => context_course::instance($this->course->id),
        ]);
    }

    /**
     * Get course_module_viewed event.
     * 
     * @param string $type Type of module.
     * @return course_module_viewed
     */
    public function course_module_viewed($type) {
        $this->set_course();
        $this->module = $this->generator->create_module($type, array('course' => $this->course->id));
        $class = '\mod_' . $type . '\event\course_module_viewed';
        $params = [
            'objectid' => $this->module->id,
            'context' => context_module::instance($this->module->cmid),
        ];
        switch ($type) {
            case 'feedback':
                $params['other'] = ['anonymous' => FEEDBACK_ANONYMOUS_YES];
                break;

            case 'survey':
                $params['other'] = ['viewed' => 'What was viewed'];
                break;
        }
        return $class::create($params);
    }

    /**
     * Set the course property.
     */
    protected function set_course() {
        if (!isset($this->course)) {
            $this->course = $this->generator->create_course();
        }
    }

}
