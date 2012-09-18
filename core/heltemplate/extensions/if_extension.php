<?php
/**
 * Extension for if function handling
 *
 * @author Hendrik Schaeidt
 * @version 1 - 18.09.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2010
 */
 namespace core\heltemplate\extensions;
 use core\heltemplate\helTemplate;
 use core\heltemplate\extensions\vartrans;
 use core\heltemplate\interfaces\helTemplateExtension;
 
class if_extension implements helTemplateExtension {
    
    /**
     * Instance of helTemplate
     */
    private $_oTemplate = null;
    
    /**
     * Patterns for extension keys.
     * If the pattern matches, the equivalent $_aReplaced
     * will be called
     */
    private $_aPattern = array(
        '/^if (.+)/',
        '/^elseif (.+)/',
        '/^;else$/',
    );
    
    /**
     * Patterns to replace the matches
     */
    private $_aReplace = array(
        '',
        '',
        '}else{',
    );
    
    /**
     * Constructor
     *
     * @param helTemplate $oTemplate: Must be object of helTemplate
     * @return void
     */
    public function __construct($oTemplate) {
        $this->_oTemplate = $oTemplate;
    }
 
    /**
     * Returns an array with all Pattern
     *
     * @return Array $aSearchPattern
     */
    public function getSearchPattern(){
        return $this->_aPattern;
    }
    
    /**
     * Returns an array with the needed replace
     *
     * @param Int $iKey
     * @return String $sReplace
     */
    public function getReplace($iKey, $bIsFromController = true){
        $sReplace = $this->_replace();
        if($sReplace !== null) {
            return $sReplace;
        } else {
            return $this->_aReplace[$iKey];
        }
    }
    
    /**
     * Replaces all conditions (vars, strings, ints) with the correspondent
     * vartransformer
     *
     * @return String $sReplaced: full builded condition to replace with snippet
     */
    public function _replace() {
        $aVars = array();
        $aVars = explode(" ", $this->sToCompile);
        if($aVars[0] == ";else") {
            return null;
        }
        $bReplaced = false;
        $sReplaced = "";
        if($aVars[0] == "elseif") {
            $sReplaced .= "}";
        }
        
        $sReplaced .= $aVars[0];
        if($aVars[0] == "elseif" || $aVars[0] == "if") {
            $sReplaced .= '(';
        }
        $oVartrans = new vartrans($this->_oTemplate);
        $aPattern = $oVartrans->getSearchPattern();
        foreach($aVars as $sVar) {
            if($sVar != $aVars[0]) {
                $bReplaced = false;
                for($i = 0; $i < count($aPattern); $i++) {
                    if(preg_match($aPattern[$i], $sVar) && $bReplaced === false) {
                        $sReplace = $oVartrans->getReplace($i, false);
                        $sReplaced .= preg_replace($aPattern[$i], $sReplace, $sVar);
                        $bReplaced = true;
                    }
                }
                
                if($bReplaced === false) {
                    $sReplaced .= " ". $sVar ." ";
                }
                
                $bReplaced = false;
            }
        }
        if($aVars[0] == "elseif" || $aVars[0] == "if") {
            $sReplaced .= ')';
        }
        $sReplaced .= "{";
        return $sReplaced;
    }
}