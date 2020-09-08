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
 * Question type class for the Kekule Chem question type.
 *
 * @package    qtype_kekule_chem
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/kekule_chem_base/lib.php');
require_once($CFG->dirroot . '/question/type/kekule_chem_base/questiontype.php');
/*
class qtype_kekule_chem_multi_answer extends qtype_kekule_chem_base_answer {
    public $comparelevel;
    public $comparemethod;
    public $arrows_transfo;

    public function __construct($id, $answer, $fraction, $feedback, $feedbackformat, $blankindex, $comparelevel, $comparemethod, $arrows_transfo) {
        parent::__construct($id, $answer, $fraction, $feedback, $feedbackformat, $blankindex, $comparelevel, $comparemethod);
        $this->comparelevel = $comparelevel;
        $this->comparemethod = $comparemethod;
        $this->arrows_transfo = $arrows_transfo;
    }
}
*/

/**
 * The Kekule Chem question type.
 */
class qtype_kekule_chem_multi extends qtype_kekule_chem_base {
    public function menu_name() {
        return $this->local_name();
    }
    
    public function save_question_options($question) {
        foreach ($question->answer as $id => $q) {
            $concat = array($question->answer[$id], $question->answer_2[$id]);
            $question->answer[$id] = json_encode($concat);
        }

        parent::save_question_options($question);   
    }
    
    public function extra_answer_fields() {
        return array('qtype_kekule_ans_ops_multi', 'blankindex','arrows_transfo','next_to_arrow','arrows_grade','draw_grade');
    }
}
