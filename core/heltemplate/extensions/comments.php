<?php
/**
 * Extension for comments
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
 use core\heltemplate\interfaces\helTemplateExtension;
 
class comments implements helTemplateExtension {
    
    /**
     * Instance of helTemplate
     */
    private $_oHelTemplate = null;
    
    /**
     * Patterns for extension keys.
     * If the pattern matches, the equivalent $_aReplaced
     * will be called
     */
    private $_aPattern = array(
        '/^\*(.+)\*$/',
    );
    
    /**
     * Patterns to replace the matches
     */
    private $_aReplace = array(
        '',
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
     * @return String $sSearchPattern
     */
    public function getReplace($iKey, $bIsFromCompiler = true){
        return $this->_aReplace[$iKey];
    }
}