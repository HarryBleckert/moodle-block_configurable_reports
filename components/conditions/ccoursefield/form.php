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

class ccoursefield_form extends moodleform {
    public $allowedops = [
        '=' => '=',
        '>' => '>',
        '<' => '<',
        '>=' => '>=',
        '<=' => '<=',
        '<>' => '<>',
        'LIKE' => 'LIKE',
        'NOT LIKE' => 'NOT LIKE',
        'LIKE % %' => 'LIKE % %'
    ];

    public function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header',  'crformheader', get_string('coursefield', 'block_configurable_reports'), '');

        $columns = $DB->get_columns('course');

        $coursecolumns = [];
        foreach ($columns as $c) {
            $coursecolumns[$c->name] = $c->name;
        }

        $mform->addElement('select', 'field', get_string('column', 'block_configurable_reports'), $coursecolumns);

        $mform->addElement('select', 'operator', get_string('operator', 'block_configurable_reports'), $this->allowedops);
        $mform->addElement('text', 'value', get_string('value', 'block_configurable_reports'));
        $mform->setType('value', PARAM_RAW);
        // Buttons.
        $this->add_action_buttons(true, get_string('add'));

    }

    public function validation($data, $files) {
        global $DB, $db, $CFG;

        $errors = parent::validation($data, $files);

        if (!in_array($data['operator'], $this->allowedops)) {
            $errors['operator'] = get_string('error_operator', 'block_configurable_reports');
        }

        $columns = $DB->get_columns('course');
        $coursecolumns = array();
        foreach ($columns as $c) {
            $coursecolumns[$c->name] = $c->name;
        }

        if (!in_array($data['field'], $coursecolumns)) {
            $errors['field'] = get_string('error_field', 'block_configurable_reports');
        }

        if (!is_numeric($data['value']) && preg_match('/^(<|>)[^(<|>)]/i', $data['operator'])) {
            $errors['value'] = get_string('error_value_expected_integer', 'block_configurable_reports');
        }

        return $errors;
    }
}