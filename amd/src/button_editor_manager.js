/* eslint no-undef: "off"*/
define(['jquery'], function($) {
    return {
        init: function() {
            $('span[nexot="0"] .K-Chem-Viewer-Embedded-Toolbar .K-Chem-Viewer-Edit').click(function() {
              $(".K-Chem-MolRingIaController ").css("display","none");
              $(".K-Chem-ArrowLineIaController-ReactionArrowNormal").css("display","none");
              $(".K-Chem-ArrowLineIaController-ReactionArrowReversible").css("display","none");
              $(".K-Chem-ArrowLineIaController-ReactionArrowResonance").css("display","none");
              $(".K-Chem-ArrowLineIaController-ReactionArrowRetrosynthesis").css("display","none");
              $(".K-Chem-ArrowLineIaController-Line").css("display","none");
              $(".K-Chem-ArrowLineIaController-BondFormingElectronPushingArrowSingle").css("display","none");
              $(".K-Chem-ArrowLineIaController-HeatSymbol").css("display","none");
              $(".K-Chem-ArrowLineIaController-AddSymbol").css("display","none");
              $(".K-Chem-MolRingIaController").css("display","none");
              $(".K-Chem-MolAtomIaController").css("display","none");
              $(".K-Chem-MolBondIaController").css("display","none");
              $(".K-Chem-MolNodeChargeIaController").css("display","none");
              $(".K-Chem-TextImageIaController").css("display","none");
            });
            
            $('span[nexot="1"] .K-Chem-Viewer-Embedded-Toolbar .K-Chem-Viewer-Edit').click(function() {
              $(".K-Chem-MolRingIaController ").css("display","block");
              $(".K-Chem-ArrowLineIaController-ReactionArrowNormal").css("display","block");
              $(".K-Chem-ArrowLineIaController-ReactionArrowReversible").css("display","block");
              $(".K-Chem-ArrowLineIaController-ReactionArrowResonance").css("display","block");
              $(".K-Chem-ArrowLineIaController-ReactionArrowRetrosynthesis").css("display","block");
              $(".K-Chem-ArrowLineIaController-Line").css("display","block");
              $(".K-Chem-ArrowLineIaController-BondFormingElectronPushingArrowSingle").css("display","block");
              $(".K-Chem-ArrowLineIaController-HeatSymbol").css("display","block");
              $(".K-Chem-ArrowLineIaController-AddSymbol").css("display","block");
              $(".K-Chem-MolRingIaController").css("display","block");
              $(".K-Chem-MolAtomIaController").css("display","block");
              $(".K-Chem-MolBondIaController").css("display","block");
              $(".K-Chem-MolNodeChargeIaController").css("display","block");
              $(".K-Chem-TextImageIaController").css("display","block");
            });
        }
    }
});