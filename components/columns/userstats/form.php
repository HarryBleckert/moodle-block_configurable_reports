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

if (!defined('MOODLE_INTERNAL')) {
    //  It must be included from a Moodle page.
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/formslib.php');

class userstats_form extends moodleform {
    public function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'crformheader', get_string('userstats', 'block_configurable_reports'), '');

        $userstats = array(
            'logins' => get_string('statslogins', 'block_configurable_reports'),
            'activityview' => get_string('activityview', 'block_configurable_reports'),
            'activitypost' => get_string('activitypost', 'block_configurable_reports')
        );
        $userstats['coursededicationtime'] = get_string('coursededicationtime', 'block_configurable_reports');

        $mform->addElement('select', 'stat', get_string('stat', 'block_configurable_reports'), $userstats);

        $limitoptions = array();
        for ($i = 5; $i <= 150; $i += 5) {
            $limitoptions[$i * 60] = $i;
        }
        $mform->addElement('select', 'sessionlimittime', get_string('sessionlimittime', 'block_configurable_reports'),
                            $limitoptions);
        $mform->addHelpButton('sessionlimittime', 'sessionlimittime', 'block_configurable_reports');
        $mform->setDefault('sessionlimittime', 30 * 60);
        $mform->disabledIf('sessionlimittime', 'stat', 'neq', 'coursededicationtime');

        $this->_customdata['compclass']->add_form_elements($mform, $this);

        // Buttons.
        $this->add_action_buttons(true, get_string('add'));

    }

    public function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);

        $errors = $this->_customdata['compclass']->validate_form_elements($data, $errors);

        if ($data['stat'] != 'coursededicationtime' && (!isset($CFG->enablestats) || !$CFG->enablestats)) {
            $errors['stat'] = get_string('globalstatsshouldbeenabled', 'block_configurable_reports');
        }

        return $errors;
    }
}
