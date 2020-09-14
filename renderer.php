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
 * YOURQTYPENAME question renderer class.
 *
 * @package    qtype
 * @subpackage YOURQTYPENAME
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/kekule_chem_base/lib.php');
require_once($CFG->dirroot . '/question/type/kekule_chem_base/renderer.php');

class qtype_kekule_chem_multi_renderer extends qtype_kekule_chem_base_renderer {
    
    public function formulation_and_controls(question_attempt $qa,
                                             question_display_options $options) {   
        global $PAGE, $CFG;
        kekulejs_utils::includeKekuleScriptFiles();
        $PAGE->requires->js(new moodle_url($CFG->wwwroot .'/question/type/kekule_chem_base/scripts/render.js'));
      
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        
        //$inputBaseName = $qa->get_qt_field_name('answer');
        if ($options->correctness) {  // need to score the whole question first
            $question->grade_response($response);
            $correctResponse = $question->get_correct_response();
        }
        else
            $correctResponse = null;

        $result = '';
        $blankIndex = 0;
        $answers = array();

        foreach ($question->questionParts as $index => $subPart)
        {
            if ($subPart->role === qtype_kekule_multianswer_part::TEXT)  // normal text
            {
                $sPart = html_writer::span($subPart->content);
            }
            else if ($subPart->role === qtype_kekule_multianswer_part::BLANK)  // blank place holder
            {
                $answerFieldName = $this->getAnswerFieldName($blankIndex);
                $currentAnswer = $qa->get_last_qt_var($answerFieldName);
                
                if (!isset($currentAnswer)) {
                //remove arrow of answer              
                        $a = json_decode(reset($question->answers)->answer);
                        
                        $a0 = json_decode($a[0]);
                        $a1 = json_decode($a[1]);
                        $a1->molData = "";
                        $a1->smiles="";
                        $a1->smilesNoStereo="";
                        $moldata = json_decode($a0->molData);

                        foreach ($moldata->root->children->items as $id=> $i) {
                            if ($i->__type__ == "Kekule.Glyph.ElectronPushingArrow") {
                                unset($moldata->root->children->items[$id]);
                            }
                        }
                        
                        $a0->molData = json_encode($moldata);
                        $a[0] = $a0;
                        $a[1] = $a1;
                        $answeWithoutArrow = json_encode($a);
                  //      var_dump($answeWithoutArrow);die();
                        $currentAnswer = $answeWithoutArrow;
                }
                
                $answers[$blankIndex] = $currentAnswer;
                $sPart = $this->getBlankHtml($blankIndex, $subPart, $currentAnswer, $question, $qa, $options, $correctResponse);
                ++$blankIndex;
                //if ($needFeedback)
                {
                    // todo: need to implement feedback here
                }
            }
            if (!empty($sPart))
                $result .= $sPart;
        }

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                $question->get_validation_error($answers),
                array('class' => 'validationerror'));
        }

        $result = $qa->rewrite_pluginfile_urls($result, "question", "questiontext", $question->id);
        return $result;

    }
    
        /**
     * Returns HTML string that represent a blank in question.
     * Descendants should override this method.
     * @param $blankIndex
     * @param $blank
     * @param string $ctrlName Default name of the form control.
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return string
     */
    protected function getBlankHtml($blankIndex, $blank, $answer, $question,
                                    question_attempt $qa, question_display_options $options, $correctResponse)
    {
        global $DB, $CFG;
        $inputType = intval($question->inputtype);
        $widgetType = 'viewer';
        $htmlWidgetClassName = qtype_kekule_chem_html::CLASS_MOL_BLANK;
        $widgetInputType = qtype_kekule_chem_html::INPUT_TYPE_MOL;
        if ($inputType == qtype_kekule_chem_input_type::DOCUMENT)  // allow input document
        {
            /*
            $widgetType = 'composer';
            */
            $htmlWidgetClassName = qtype_kekule_chem_html::CLASS_DOC_BLANK;
            $widgetInputType = qtype_kekule_chem_html::INPUT_TYPE_DOC;
        }
        else // input single molecule, need to create viewer widget
        {

        }
        //var_dump($question);

        $answerFieldName = $this->getAnswerFieldName($blankIndex);
        $ctrlName = $qa->get_qt_field_name($answerFieldName);

        $inputElemAttributes = array(
            'type' => 'hidden',
            'name' => $ctrlName,
            'value' => $answer,
            'id' => $ctrlName,
            'class' => qtype_kekule_chem_html::CLASS_BLANK_ANSWER,
            'exot' => "chem_multi",
            /*'size' => 40*/
        );
        $chemElemAttributes = array(
            'data-preferWidget' => $widgetType,
            'data-name' => $ctrlName,
            'value' => $answer,
            'class' => qtype_kekule_chem_html::CLASS_BLANK,
            'data-widget-class' => $htmlWidgetClassName,
            'data-input-type' => $widgetInputType,
            'exot' => "chem_multi",
            'nexot' => 0
        );
        if ($options->readonly) {
            $inputElemAttributes['readonly'] = 'readonly';
            $chemElemAttributes['data-predefined-setting'] = 'static';
        }
        else
            $chemElemAttributes['data-predefined-setting'] = 'editOnly';
        if ($options->correctness) {
            $fraction = $question->blanks[$blankIndex]->fraction;
            $inputElemAttributes['class'] .= ' ' . $this->feedback_class($fraction);
            $chemElemAttributes['class'] .= ' ' . $this->feedback_class($fraction);
            $feedbackimg = $this->feedback_image($fraction);
        }
        else
            $feedbackimg = '';
        
        $result = html_writer::empty_tag("br");
          //editor 1
        $result .= html_writer::span('', '', $chemElemAttributes);
        
        //Middle question part
        $qa = $question->answers;
        $av = array_values($qa);
        $array_qa = array_shift($av);
        $ans_id = $array_qa->id;
        $inter_ans = $DB->get_records("qtype_kekule_ans_ops_multi",array("answerid" => $ans_id));
        $intav = array_values($inter_ans);
        $middle_datas = array_shift($intav);
        
        // Arrow
        
        if (sizeof(json_decode(json_decode($middle_datas->next_to_arrow)->molData)->root->children->items) > 0) {
            $style = "left:10px;position: relative;";
        } else {
            $style = "margin-right: 21px;left:10px;position: relative;";
        }
        
        $result .= html_writer::start_tag('span', array("style" => $style));
        $result .= html_writer::img($CFG->wwwroot."/question/type/kekule_chem_multi/img/".$middle_datas->arrows_transfo.".png","arrows_transfo");
        $result .= html_writer::end_tag('span');
//Symbols
     //   $middle_datas = array_shift(array_values($DB->get_records("qtype_kekule_ans_ops_multi",array("answerid" => array_shift(array_values($question->answers))->id))));
        $elemAttributesMiddleDatas = array(
            'type' => 'hidden',
            'name' => "middle_draw",
            'data-name' => "middle_draw", 
            'value' => $middle_datas->next_to_arrow,
            'id' => "middle_draw",
            'class' => qtype_kekule_chem_html::CLASS_BLANK,
            'data-widget-class' => $htmlWidgetClassName,
            'data-input-type' => $widgetInputType,
            'data-preferWidget' => 'static',
        );
        
            /*Dirty but worky */
        if (sizeof(json_decode(json_decode($middle_datas->next_to_arrow)->molData)->root->children->items) > 0) {
            $result .= html_writer::start_tag('input', $elemAttributesMiddleDatas);
            $result .= html_writer::end_tag('input');

            $elemAttributesMiddleDatas['style'] = "border: none;background: none;";
            $result .= html_writer::span('', '', $elemAttributesMiddleDatas);
        }
        //editor 2
        $chemElemAttributes["data-name"] = substr($chemElemAttributes["data-name"],"0","-1")."1";
        $chemElemAttributes['nexot'] = 1;
        $result .= html_writer::span('', '', $chemElemAttributes);
        
        
        //Input 1
        $result .= html_writer::start_tag('input', $inputElemAttributes);
        $result .= html_writer::end_tag('input');
        
        //Input 2
        $inputElemAttributes["id"] = $chemElemAttributes["data-name"];
  /*      $inputElemAttributes["name"] = $chemElemAttributes["data-name"];
        $result .= html_writer::start_tag('input', $inputElemAttributes);
        $result .= html_writer::end_tag('input');
        */
        $result .= $feedbackimg;
        /*
        $result .= parent::getBlankHtml($blankIndex, $blank, $answer, $question,
            $qa, $options, $correctResponse);
        */
        return $result;
    }
    
        protected function correctResponseTextToHtml(question_attempt $qa, $question, $text)
    {
        global $DB, $CFG;
            
        $anstotal = json_decode($text);

        $ansDetail1 = $question->parseAnswerString($anstotal[0]);
        $ansDetail2 = $question->parseAnswerString($anstotal[1]);
        // create auto launch viewer widget
        $attr = array(
            'data-widget' => 'Kekule.ChemWidget.Viewer',
            'data-auto-size' => 'true',
            'data-predefined-setting' => 'static'
        );
        
        $html = "";
        
        if (!empty($ansDetail1->molData)) {
            $attr['data-chem-obj'] = $ansDetail1->molData;
            $html .= html_writer::span('', qtype_kekule_chem_html::CLASS_CORRECT_RESPONSE, $attr);
        }
        
        $ak = array_keys($question->answers);
        $id_key_first_question = array_shift($ak);
        $ansdata = $DB->get_record("qtype_kekule_ans_ops_multi", array("answerid" => $id_key_first_question));
        $next_to_arrow = $ansdata->next_to_arrow;
        $arrow = $ansdata->arrows_transfo;
        
        $html .= html_writer::img($CFG->wwwroot."/question/type/kekule_chem_multi/img/".$arrow.".png","arrows_transfo");
       // var_dump(json_decode($next_to_arrow)->molData);die();
        if (sizeof(json_decode(json_decode($next_to_arrow)->molData)->root->children->items) > 0) {
            $attr['data-chem-obj'] = json_decode($next_to_arrow)->molData;
            $html .= html_writer::span('', qtype_kekule_chem_html::CLASS_CORRECT_RESPONSE, $attr);
        }
        
        if (!empty($ansDetail2->molData)) {
            $attr['data-chem-obj'] = $ansDetail2->molData;
            $html .= html_writer::span('', qtype_kekule_chem_html::CLASS_CORRECT_RESPONSE, $attr);
        }
        
        return $html;
    }

}
