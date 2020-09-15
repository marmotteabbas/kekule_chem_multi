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

require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/type/kekule_chem_base/question.php');
require_once($CFG->dirroot . '/question/type/kekule_chem_base/lib.php');


/**
 * Represents a Kekule Chem question.
 */
class qtype_kekule_chem_multi_question extends qtype_kekule_chem_base_question {
    
    protected function calcMatchingRatio($responseItem, $key) {
        return $this->_compareMolAnsStringMulti($responseItem, $key->answer, $this->getAnsCompareMethod($key), $this->getAnsCompareLevel($key),$key->id);
    }
    
     protected function _compareMolAnsStringMulti($src, $target, $compareMethod, $compareLevel,$key_id) {
        global $DB;
        $target = json_decode($target);
                       
        foreach ($target as $i => $t) {
            $target[$i] = json_decode($t);
        }
        
        $targetDetail = $target;
        $srcDetail = (array)json_decode($src);
        
        $compare_one_target = json_encode($targetDetail[0]);
        $compare_one_src = json_encode($srcDetail[0]);
        
        $compare_two_target = json_encode($targetDetail[1]);
        $compare_two_src = json_encode($srcDetail[1]);
        
        $qkaom = $DB->get_record("qtype_kekule_ans_ops_multi", array("answerid"=>$key_id));
        
        $arrows_grade = floatval(parent::_compareMolAnsString($compare_one_src, $compare_one_target, 13, 2));
        $draw_grade = floatval(parent::_compareMolAnsString($compare_two_src, $compare_two_target, 1, $compareLevel));
        

        return ($arrows_grade*$qkaom->arrows_grade)+($draw_grade*$qkaom->draw_grade);
     }
     
    protected function gradeResponseOfGroup($blankGroup, $responses) {
        $responseCount = count($responses);
        // build a score matrix of keyGroups * responses
        $scoreMatrix = array();
        foreach ($blankGroup as $i => $keyIndex)
        {
            $scoreMatrix[$keyIndex] = array();
            $keys = $this->answerKeyMap[$keyIndex];
            foreach($responses as $resIndex => $response)
            {
                list($matchKey, $matchRatio, $matchRatioFraction) = $this->getMatchKeyOfResponse($response, $keys);
                $cell = new stdClass;
                $cell->matchKey = $matchKey;
                $cell->matchRatio = $matchRatio;
                $cell->matchRatioFraction = $matchRatioFraction;
                $scoreMatrix[$keyIndex][$resIndex] = $cell;
            }
        }
        //var_dump($scoreMatrix);
        // find highest match ratio element from matrix, then delete the corresponding row and col
        // do the same job on the remaining matrix, gradually got all results

        while (!empty($scoreMatrix))
        {
            list($keyIndex, $resIndex, $maxCell) = $this->_extractMaxRatioCell($scoreMatrix);
            // save result to corresponding blank
            $blankIndex = $blankGroup[$resIndex];
            $blank = $this->blanks[$blankIndex];
            //var_dump($blank);
            $blank->matchAnswerKey = $maxCell->matchKey;
            $blank->matchRatio = $maxCell->matchRatio;
            if (isset($blank->matchAnswerKey))
                $blank->fraction = $blank->matchAnswerKey->fraction * $maxCell->matchRatio;
            else
                $blank->fraction = 0;
            $blank->score = $this->getBlankDefaultMark($keyIndex) * $blank->fraction;
            //var_dump($blank);
        }
    }
    
    private function _extractMaxRatioCell(&$scoreMatrix) {
        $maxRatioFraction = -1;
        $rowIndex = -1;
        $colIndex = -1;
        $maxCell = null;
        foreach($scoreMatrix as $keyIndex => $keyRow)
        {
            foreach($keyRow as $resIndex => $cell)
            {
                if ($cell->matchRatioFraction > $maxRatioFraction)
                {
                    $maxCell = $cell;
                    $maxRatioFraction = $cell->matchRatioFraction;
                    $rowIndex = $keyIndex;
                    $colIndex = $resIndex;
                }
            }
        }
        // remove max row and col
        unset($scoreMatrix[$rowIndex]);
        foreach ($scoreMatrix as $keyIndex => $keyRow)
        {
            unset($scoreMatrix[$keyIndex][$colIndex]);
        }
        return array($rowIndex, $colIndex, $maxCell);
    }
}
