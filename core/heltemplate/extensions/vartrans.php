<?php
/**
 * Extension for variable handling
 *
 * @author Steven Zumpe & Hendrik Schaeidt
 * @version 1 - 08.06.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2010
 */
 namespace core\heltemplate\extensions;
 use core\heltemplate\interfaces\helTemplateExtension;
 
 class vartrans implements helTemplateExtension {
    
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
        '/^(null)$/',
        '/^(\w+)$/',
        '/^!(\w+)$/',
        '/^\.(\w+)\.(\w+)\((\w+)\)$/',
        '/^!\.(\w+)\.(\w+)\((\w+)\)$/',
        '/^\.(\w+)\.(\w+)\((.*)\)$/',
        '/^!\.(\w+)\.(\w+)\((.*)\)$/',
        '/^\.(\w+)\((\w+)\)$/',
        '/^!\.(\w+)\((\w+)\)$/',
        '/^\.(\w+)\((.*)\)$/',
        '/^!\.(\w+)\((.*)\)$/',
        '/^(\w+)\[(.*)\]$/',
        '/^\.(\w+)\[(.*)\]$/',
        '/^(\w+)\[(\w+)\]$/',
        '/^\.(\w+)\[(\w+)\]$/',
        
    );
    
    /**
     * Patterns to replace the matches
     */
    private $_aReplace = array(
        '\1',
        '$\1',
        '!$\1',
        '$this->\1->\2($\3)',
        '!$this->\1->\2($\3)',
        '$this->\1->\2(\3)',
        '!$this->\1->\2(\3)',
        '$this->\1($\2)',
        '!$this->\1($\2)',
        '$this->\1(\2)',
        '!$this->\1(\2)',
        '$\1[\2]',
        '$this->\1[\2]',
        '$\1[$\2]',
        '$this->\1[$\2]',
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
        if($bIsFromController === true) {
            return "echo ".$this->_aReplace[$iKey].";";
        }
        
        return $this->_aReplace[$iKey];
    }
}