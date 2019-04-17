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
 * xAPI transformation of a H5P event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\logstore_trax;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\utils;

/**
 * xAPI transformation of a H5P event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hvp_module_event_triggered extends hvp_internal_event_triggered {

    /**
     * Transform the H5P object.
     *
     * @param \stdClass $nativeobject H5P object
     * @param array $base Statement base
     * @return \stdClass
     */
    protected function transform_object($nativeobject, $base)
    {
        global $DB;

        // Change ID.
        $nativeobject->id = $base['context']['contextActivities']['parent'][0]['id'] . '/question';

        // Adapt name and description.
        $questiontitle = (array)$nativeobject->definition->description;
        $questiontitle = reset($questiontitle);
        $questiontitle = trim($questiontitle);
        unset($nativeobject->definition->description);
        unset($nativeobject->definition->name);
        $course = $DB->get_record('course', array('id' => $this->event->courseid));
        $nativeobject->definition->name = utils::lang_string($questiontitle, $course);

        // Remove extensions.
        unset($nativeobject->definition->extensions);

        return $nativeobject;
    }

}
