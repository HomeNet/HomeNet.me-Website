<?php

/*
 * Service.php
 * 
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * 
 * This file is part of HomeNet.
 * 
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * Description of Service
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Core_Model_Reflection_Service {

    private $_modules = null;

    private function getFolders($path) {
        if (!is_dir($path)) {
            throw new InvalidArgumentException('Path is not a directory');
        }

        $folders = array();

        foreach (scandir($path) as $file) {

            if (($file != '.') && ($file != '..') && is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                $folders[] = $file;
            }
        }
        return $folders;
    }

    private function getFileNames($path) {
        if (!is_dir($path)) {
            throw new InvalidArgumentException('Path is not a directory');
        }

        $files = array();

        foreach (scandir($path) as $file) {

            if (($file != '.') && ($file != '..') && !is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                $files[] = basename($file, '.phtml');
            }
        }
        return $files;
    }

    /**
     * Get array of all installed modules and their paths
     * 
     * Pulled from the front controller
     * 
     * @todo find a way to mark modules active/inactive
     * 
     * @return array
     */
    public function getModulePaths() {
        if ($this->_modules == null) {
            $front = Zend_Controller_Front::getInstance();
            $paths = $front->getControllerDirectory();

            //fix default
            //$paths['Core'] = $paths['default'];
            // unset($paths['default']);

            foreach ($paths as $key => $value) {
                $paths[$key] = dirname($value);
            }
            $this->_modules = $paths;
        }

        return $this->_modules;
    }

    /**
     * Get Module Path
     * 
     * @param type $module 
     * @return string path
     */
    public function getModulePath($module) {
        $paths = $this->getModulePaths();
        if (!array_key_exists($module, $paths)) {
            throw new Exception('Module: ' . $module . ' Not Found');
        }

        return $paths[$module];
    }

    /**
     * Get all modules, keys/ values are the same, suitable for Zend_Form_Element_Option
     * 
     * @return array 
     */
    public function getModules() {

        $modules = $this->getModulePaths();

        foreach ($modules as $key => $value) {
            $modules[$key] = $key;
        }

        return $modules;
    }

    /**
     * Get array of the installers for all modules
     * 
     * @return CMS_Installer_Abstract[]
     */
    public function getModuleInstallers() {

        $installers = array();

        $paths = $this->getModulePaths();

        foreach ($paths as $module => $path) {

            if (!file_exists($path . DIRECTORY_SEPARATOR . 'Installer.php')) {
                //@todo log failure so admin can fix
                continue;
            }

            include_once $path . DIRECTORY_SEPARATOR . 'Installer.php';

            //  if ($module == 'Core') {
            //      $class = 'Installer';
            //  } else {
            $class = $module . '_Installer';
            //  }

            $installers[$module] = new $class();
        }

        return $installers;
    }

    public function getModuleInstaller($module) {

        $module = ucfirst($module);

        $paths = $this->getModulePaths();

        if (empty($paths[$module])) {
            throw new Exception('Can not find module: ' . $module);
        }

        if (!file_exists($paths[$module] . DIRECTORY_SEPARATOR . 'Installer.php')) {
            //@todo log failure so admin can fix
            throw new Exception('Can not find installer for module: ' . $module);
        }

        include_once $paths[$module] . DIRECTORY_SEPARATOR . 'Installer.php';

        if ($module == 'Core') {
            $class = 'Installer';
        } else {
            $class = $module . '_Installer';
        }


        Zend_Loader::loadClass($class);
        $installer = new $class();

        return $installer;
    }

    public function getModuleConfig($module) {

        $module = ucfirst($module);

//        $class = $module.'_Installer';
//        
//        Zend_Loader::loadClass($class);
//        $installer =  new $class();

        return $config;
    }

    public function getControllersByModule($module) {
        
    }

    public function getPluginTypesByModule($module) {

        $plugins = array();

        $path = $this->getModulePath($module);

        $path .= DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR;

        $plugins = $this->getFolders($path);

        return $plugins;
    }

    public function getPluginConfigsByModuleType($module = null, $type) {
        $configs = array();

        $path = $this->getModulePath($module);

        $path .= DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . ucfirst($type);

        if (!is_dir($path)) {
            throw new Exception('Module: ' . $module . ' Type: ' . $type . ' Not Found');
        }

        foreach (scandir($path) as $file) {

            if (($file != '.') && ($file != '..') && is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                $iniPath = $path . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . 'plugin.ini';
                if (file_exists($iniPath)) {
                    // require_once($iniPath);
                    // $className = 'Content_Plugin_Template_' . $file . '_Installer';
                    // if (class_exists($className)) {
                    //$templates[$file] = $className;
                    $configs[$file] = new Zend_Config_Ini($iniPath);
                    //$templates[$file] = $config->toArray();
                     } else {
                         throw new Exception('Can Not load Template, Class DNE: '.$file. ' Path:'.$iniPath );
                    // }
                } //throw new Exception('Can Not load Template: '.$classPath );
            }
        }
        return $configs;
    }

    public function getActionsByModuleController($module, $controller) {
        
    }

    public function getLayouts($theme = null) {
        if ($theme === null) {
            $config = Zend_Registry::get('config');
            $theme = $config->site->defaultTheme;
        }
        // die(APPLICATION_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'scripts');
        return $this->getFileNames(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'layouts');
    }

    public function getThemes() {
        return $this->getFolders(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'themes');
    }

    public function getThemeInstaller($template) {
        
    }

}