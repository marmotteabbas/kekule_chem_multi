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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/kekule_chem_base/lib.php');
require_once($CFG->dirroot . '/question/type/kekule_chem_base/edit_kekule_chem_base_form.php');

class qtype_kekule_chem_multi_edit_form extends qtype_kekule_chem_base_edit_form {
    
    protected function definition_inner($mform) {
        global $PAGE;
        $PAGE->requires->css('/question/type/kekule_chem_multi/selectimag.css');
        $PAGE->requires->css('/question/type/kekule_chem_multi/nexttoarrow.css');
        parent::definition_inner($mform);
    }
    public function qtype() {
        return 'kekule_chem_multi';
    }
    
    protected function data_preprocessing($question) {
        $question = $this->data_preprocessing_answers($question);
        
        if (isset($question->answer)) {
            foreach ($question->answer as $i=>$qa) {
                $multi_a = json_decode($qa);
                if (!is_object($multi_a)) {
                    $question->answer[$i] = $multi_a[0];
                    $question->answer_2[$i] = $multi_a[1];
                }
            }
        }
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }
    
    protected function getAnswerDataFormControls($mform, $label, $gradeoptions/*, $ansIndex*/)
    {
        global $PAGE;
        
        $result = array();
        
        $result[] = $mform->createElement('textarea', 'answer', get_string('captionMolecule', 'qtype_kekule_chem_base'),
            'class="' . qtype_kekule_chem_html::CLASS_DESIGN_ANSWER_BLANK . '" data-widget-class="' . qtype_kekule_chem_html::CLASS_DESIGN_VIEWER_BLANK . '"');
        
        $result[] = $mform->createElement('select', 'arrows_transfo',
            "",
            array(
                "simple_arrow" => "",
                "double_arrow" => ""
                ),
                ['class' => 'vodiapicker']
        );
        
        $result[] = $mform->createElement('html', '<div class="arrow_selector">
		<span class="btn-select">
			<li>
				<img src="type/kekule_chem_multi/img/simple_arrow.png" alt="" value="en">
			</li>
		</span>
		<div class="b">
			<ul id="a">
				<li>
					<img src="type/kekule_chem_multi/img/simple_arrow.png" alt="" value="simple_arrow">
				</li>
				<li>
					<img src="type/kekule_chem_multi/img/double_arrow.png" alt="" value="double_arrow">
				</li>
		</div>
	</div>');
        
        $result[] = $mform->createElement('textarea', 'next_to_arrow', get_string('captionMolecule', 'qtype_kekule_chem_base'),
            'class="' . qtype_kekule_chem_html::CLASS_DESIGN_ANSWER_BLANK . '" data-widget-class="' . qtype_kekule_chem_html::CLASS_DESIGN_VIEWER_BLANK . '"');
        
        
        $result[] = $mform->createElement('textarea', 'answer_2', get_string('captionMolecule', 'qtype_kekule_chem_base'),
            'class="' . qtype_kekule_chem_html::CLASS_DESIGN_ANSWER_BLANK . '" data-widget-class="' . qtype_kekule_chem_html::CLASS_DESIGN_VIEWER_BLANK . '"');
        
        $result[] = $mform->createElement('select', 'arrows_grade',
            "Arrows Grade",
            array(
                "0.0" => "0%",
                "0.25" => "25%",
                "0.5" => "50%",
                "0.75" => "75%",
                "1.0" => "100%"
                )
        );      
        
        $result[] = $mform->createElement('select', 'draw_grade',
            "Draw Grade",
            array(
                "0.0" => "0%",
                "0.25" => "25%",
                "0.5" => "50%",
                "0.75" => "75%",
                "1.0" => "100%"
                )
        );
        
        /*
        $result[] = $mform->createElement('select', 'comparelevel',
            get_string('captionCompareLevel', 'qtype_kekule_chem_base'),
            array(
                qtype_kekule_chem_compare_levels::DEF_LEVEL => get_string('molCompareLevelDefault', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_levels::CONSTITUTION => get_string('molCompareLevelConstitution', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_levels::CONFIGURATION => get_string('molCompareLevelConfiguration', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_levels::NON_BONDING_PAIRS => get_string('molCompareLevelElectronMatters', 'qtype_kekule_chem_base')
                )
        );

        $result[] = $mform->createElement('select', 'comparemethod',
            get_string('captionCompareMethod', 'qtype_kekule_chem_base'),
            array(
                qtype_kekule_chem_compare_methods::DEF_METHOD => get_string('molCompareMethodDefault', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_methods::SMILES => get_string('molCompareMethodSmiles', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_methods::PARENTOF => get_string('molCompareMethodParentOf', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_methods::CHILDOF => get_string('molCompareMethodChildOf', 'qtype_kekule_chem_base')
            )
        );

        $result[] = $mform->createElement('select', 'comparemethod',
            get_string('captionCompareMethod', 'qtype_kekule_chem_base'),
            array(
                qtype_kekule_chem_compare_methods::DEF_METHOD => get_string('molCompareMethodDefault', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_methods::SMILES => get_string('molCompareMethodSmiles', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_methods::PARENTOF => get_string('molCompareMethodParentOf', 'qtype_kekule_chem_base'),
                qtype_kekule_chem_compare_methods::CHILDOF => get_string('molCompareMethodChildOf', 'qtype_kekule_chem_base'),
                "13" => "Arrow"
            )
        );
        */
        $PAGE->requires->js_call_amd("qtype_kekule_chem_multi/selectimag", "init");

        return $result;

    }
}
