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
 * Configurable Reports
 * A Moodle block for creating customizable reports
 * @package:    block_configurable_reports
 * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @author: Harry Bleckert Harry@Bleckert.com from 2023 onwards
 * @author: Harry Bleckert Harry@Bleckert.com from 2023 onwards
 * @date: 2009
 */

require_once($CFG->dirroot.'/blocks/configurable_reports/plugin.class.php');

class plugin_coursefieldorder extends plugin_base{

    public $sql = true;

    public function init() {
        $this->fullname = get_string('coursefield', 'block_configurable_reports');
        $this->form = true;
        $this->unique = true;
        $this->reporttypes = array('courses');
        $this->sql = true;
    }

    public function summary($data) {
        return get_string($data->column).' '.(strtoupper($data->direction));
    }

    // Data -> Plugin configuration data.
    public function execute($data) {
        global $DB, $CFG;

        if ($data->direction == 'asc' || $data->direction == 'desc') {
            $direction = strtoupper($data->direction);
            $columns = $DB->get_columns('course');

            $coursecolumns = array();
            foreach ($columns as $c) {
                $coursecolumns[$c->name] = $c->name;
            }

            if (isset($coursecolumns[$data->column])) {
                return $data->column.' '.$direction;
            }
        }

        return '';
    }
}
