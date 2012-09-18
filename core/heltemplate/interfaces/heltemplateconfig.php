<?php
/** 
 * Every instance of helTemplate will need a Config Instance,
 * you have to write you own and inject it.
 *
 * @author Hendrik Schaeidt
 * @version 1 - 26.08.2011
 *
 * This is open source. Just keep copyright assignement and enjoy.
 *
 * © Hendrik Schaeidt & Steven Zumpe 2011
 */
 
namespace core\heltemplate\interfaces;
 
interface helTemplateConfig {
    /**
     * Returns the path to the templates to compile
     */
    public function getTemplatePath($sSelected);
    
    /**
     * Returns the Temp Path for compiled templates
     */
    public function getTemplateTmpPath($sSelected);
    
    /**
     * Returns the absolute path to the helTemplate folder
     */
    public function getTemplatePluginPath();
}