<?php
/**
 * Extension for require handling
 * If a require is identified, the it will be recompiled as soon.
 *
 * @author Steven Zumpe & Hendrik Schaeidt
 * @version 1 - 16.09.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2010
 */
 namespace core\heltemplate\extensions;
 use core\heltemplate\helTemplate;
 use core\heltemplate\templateConfig;
 use core\heltemplate\interfaces\helTemplateExtension;
 
 class requireextension implements helTemplateExtension {
    
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
        '/require (.+)/',
    );
    
    /**
     * Patterns to replace the matches
     */
    private $_aReplace = array();
    
    /**
     * Active selection basic/admin
     */
    public $sSelected = null;
    
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
        if($this->compileInclude() === true) {
            return 'require("'.$this->_oTemplate->getTmpPath().'\1");';
        } else {
            return '';
        }
    }
    
    /**
     * Launch the compilation of an include file
     *
     * @return boolean
     */
    private function compileInclude() {
        $aInclude = explode(" ", $this->sToCompile);
        $sInclude = $aInclude[1];
        $templateConfig = new templateConfig();
        $oTemplate = new helTemplate($templateConfig, $this->_oTemplate->getSelected(), $sInclude);
        if($oTemplate->checkRender() == $this->_oTemplate->getTmpPath() . $sInclude) {
            return true;
        }
    }
 }