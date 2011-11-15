<?php
/*
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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package CMS
 * @subpackage View
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class CMS_View_Helper_Breadcrumbs extends Zend_View_Helper_Abstract
{
          /**
     * Container to operate on by default
     *
     * @var Zend_Navigation_Container
     */
    protected static $_container;

    /**
     * Translator
     *
     * @var Zend_Translate_Adapter
     */
    protected $_translator;

    /**
     * ACL to use when iterating pages
     *
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * Wheter invisible items should be rendered by this helper
     *
     * @var bool
     */
    protected $_renderInvisible = false;

    /**
     * ACL role to use when iterating pages
     *
     * @var string|Zend_Acl_Role_Interface
     */
    protected $_role;

    /**
     * Whether translator should be used for page labels and titles
     *
     * @var bool
     */
    protected $_useTranslator = true;

    /**
     * Whether ACL should be used for filtering out pages
     *
     * @var bool
     */
    protected $_useAcl = true;

    /**
     * Default ACL to use when iterating pages if not explicitly set in the
     * instance by calling {@link setAcl()}
     *
     * @var Zend_Acl
     */
    protected static $_defaultAcl;

    /**
     * Default ACL role to use when iterating pages if not explicitly set in the
     * instance by calling {@link setRole()}
     *
     * @var string|Zend_Acl_Role_Interface
     */
    protected static $_defaultRole;
    
    /**
     * Breadcrumbs separator string
     *
     * @var string
     */
    protected $_separator = ' &gt; ';

   

    /**
     * Whether last page in breadcrumb should be hyperlinked
     *
     * @var bool
     */
    protected $_linkLast = false;

    /**
     * Partial view script to use for rendering menu
     *
     * @var string|array
     */
    protected $_partial;

    
    function __construct() {
        self::$_container = new Zend_Navigation();
    }
    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zend_Navigation_Container $container     [optional] container to
     *                                                  operate on
     * @return Zend_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                  returns self
     */
    public function breadcrumbs()
    {
        return $this;
    }
    
     /**
     * Magic overload: Proxy to {@link render()}.
     *
     * This method will trigger an E_USER_ERROR if rendering the helper causes
     * an exception to be thrown.
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::__toString()}.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            trigger_error($msg, E_USER_ERROR);
            return '';
        }
    }

    // Accessors:

    /**
     * Sets breadcrumb separator
     *
     * @param  string $separator                        separator string
     * @return Zend_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                  returns self
     */
    public function setSeparator($separator)
    {
        if (is_string($separator)) {
            $this->_separator = $separator;
        }

        return $this;
    }

    /**
     * Returns breadcrumb separator
     *
     * @return string  breadcrumb separator
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Sets whether last page in breadcrumbs should be hyperlinked
     *
     * @param  bool $linkLast                           whether last page should
     *                                                  be hyperlinked
     * @return Zend_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                  returns self
     */
    public function setLinkLast($linkLast)
    {
        $this->_linkLast = (bool) $linkLast;
        return $this;
    }

    /**
     * Returns whether last page in breadcrumbs should be hyperlinked
     *
     * @return bool  whether last page in breadcrumbs should be hyperlinked
     */
    public function getLinkLast()
    {
        return $this->_linkLast;
    }
    
    public function setPages($mixed){
        self::$_container = new Zend_Navigation($mixed);
    }
    
    public function addPage($mixed){
        self::$_container->addPage($mixed);
    }
    
    public function addPages($mixed){
        self::$_container->addPages($mixed);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * Sets which partial view script to use for rendering menu
     *
     * @param  string|array $partial                    partial view script or
     *                                                  null. If an array is
     *                                                  given, it is expected to
     *                                                  contain two values;
     *                                                  the partial view script
     *                                                  to use, and the module
     *                                                  where the script can be
     *                                                  found.
     * @return Zend_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                  returns self
     */
    public function setPartial($partial)
    {
        if (null === $partial || is_string($partial) || is_array($partial)) {
            $this->_partial = $partial;
        }

        return $this;
    }

    /**
     * Returns partial view script to use for rendering menu
     *
     * @return string|array|null
     */
    public function getPartial()
    {
        return $this->_partial;
    }

    // Render methods:

    /**
     * Renders breadcrumbs by chaining 'a' elements with the separator
     * registered in the helper
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               render. Default is to
     *                                               render the container
     *                                               registered in the helper.
     * @return string                                helper output
     */
    public function renderStraight()
    {
        $container = $this->getContainer();
        $container->rewind();
        
        if($container->count() == 0){
            return '';
        }
        
        //first
        
        
        $html = array();
        
       // $this->htmlify($container->current());
      //  $container->next();
        
        $items = $container->count() - 1;

        while($items > 0){
            $html[] = $this->htmlify($container->current());
            $container->next();
            $items--;
        }
        
        //last
        $active = $container->current();
        if ($this->getLinkLast()) {
            $html[] = $this->htmlify($active);
        } else {
            $temp = $active->getLabel();
            if ($this->getUseTranslator() && $t = $this->getTranslator()) {
                $temp = $t->translate($temp);
            }
            $html[] = $this->view->escape($temp);
        }
        return implode($this->getSeparator(), $html);
        //return strlen($html) ? $this->getIndent() . $html : '';
    }

    /**
     * Renders the given $container by invoking the partial view helper
     *
     * The container will simply be passed on as a model to the view script,
     * so in the script it will be available in <code>$this->container</code>.
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               pass to view script.
     *                                               Default is to use the
     *                                               container registered in the
     *                                               helper.
     * @param  string|array             $partial     [optional] partial view
     *                                               script to use. Default is
     *                                               to use the partial
     *                                               registered in the helper.
     *                                               If an array is given, it is
     *                                               expected to contain two
     *                                               values; the partial view
     *                                               script to use, and the
     *                                               module where the script can
     *                                               be found.
     * @return string                                helper output
     */
    public function renderPartial()
    {
        $container = $this->getContainer();
        $partial = $this->getPartial();
    
        if (empty($partial)) {
            require_once 'Zend/View/Exception.php';
            $e = new Zend_View_Exception(
                'Unable to render menu: No partial view script provided'
            );
            $e->setView($this->view);
            throw $e;
        }

//        // put breadcrumb pages in model
//        $model = array('pages' => array());
//        if ($active = $this->findActive($container)) {
//            $active = $active['page'];
//            $model['pages'][] = $active;
//            while ($parent = $active->getParent()) {
//                if ($parent instanceof Zend_Navigation_Page) {
//                    $model['pages'][] = $parent;
//                } else {
//                    break;
//                }
//
//                if ($parent === $container) {
//                    // break if at the root of the given container
//                    break;
//                }
//
//                $active = $parent;
//            }
//            $model['pages'] = array_reverse($model['pages']);
//        }

        if (is_array($partial)) {
            if (count($partial) != 2) {
                require_once 'Zend/View/Exception.php';
                $e = new Zend_View_Exception(
                    'Unable to render menu: A view partial supplied as '
                    .  'an array must contain two values: partial view '
                    .  'script and module where script can be found'
                );
                $e->setView($this->view);
                throw $e;
            }

            return $this->view->partial($partial[0], $partial[1], $model);
        }

        return $this->view->partial($partial, null, $model);
    }

    // Zend_View_Helper_Navigation_Helper:

    /**
     * Renders helper
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::render()}.
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               render. Default is to
     *                                               render the container
     *                                               registered in the helper.
     * @return string                                helper output
     */
    public function render()
    {
        if ($this->getPartial()) {
            return $this->renderPartial();
        } else {
            return $this->renderStraight();
        }
    }
    
    //////////////////////////////////////////////
  

    // Accessors:

    /**
     * Sets navigation container the helper operates on by default
     *
     * Implements {@link Zend_View_Helper_Navigation_Interface::setContainer()}.
     *
     * @param  Zend_Navigation_Container $container        [optional] container
     *                                                     to operate on.
     *                                                     Default is null,
     *                                                     meaning container
     *                                                     will be reset.
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setContainer(Zend_Navigation_Container $container = null)
    {
        self::$_container = $container;
        return $this;
    }

    /**
     * Returns the navigation container helper operates on by default
     *
     * Implements {@link Zend_View_Helper_Navigation_Interface::getContainer()}.
     *
     * If a helper is not explicitly set in this helper instance by calling
     * {@link setContainer()} or by passing it through the helper entry point,
     * this method will look in {@link Zend_Registry} for a container by using
     * the key 'Zend_Navigation'.
     *
     * If no container is set, and nothing is found in Zend_Registry, a new
     * container will be instantiated and stored in the helper.
     *
     * @return Zend_Navigation_Container  navigation container
     */
    public function getContainer()
    {
//        if (null === self::$_container) {
//            // try to fetch from registry first
//            require_once 'Zend/Registry.php';
//            if (Zend_Registry::isRegistered('Zend_Navigation')) {
//                $nav = Zend_Registry::get('Zend_Navigation');
//                if ($nav instanceof Zend_Navigation_Container) {
//                    return self::$_container = $nav;
//                }
//            }
//
//            // nothing found in registry, create new container
//            require_once 'Zend/Navigation.php';
//            self::$_container = new Zend_Navigation();
//        }

        return self::$_container;
    }

    /**
     * Sets the minimum depth a page must have to be included when rendering
     *
     * @param  int $minDepth                               [optional] minimum
     *                                                     depth. Default is
     *                                                     null, which sets
     *                                                     no minimum depth.
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setMinDepth($minDepth = null)
    {
        if (null === $minDepth || is_int($minDepth)) {
            $this->_minDepth = $minDepth;
        } else {
            $this->_minDepth = (int) $minDepth;
        }
        return $this;
    }

    /**
     * Returns minimum depth a page must have to be included when rendering
     *
     * @return int|null  minimum depth or null
     */
    public function getMinDepth()
    {
        if (!is_int($this->_minDepth) || $this->_minDepth < 0) {
            return 0;
        }
        return $this->_minDepth;
    }

    /**
     * Sets the maximum depth a page can have to be included when rendering
     *
     * @param  int $maxDepth                               [optional] maximum
     *                                                     depth. Default is
     *                                                     null, which sets no
     *                                                     maximum depth.
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setMaxDepth($maxDepth = null)
    {
        if (null === $maxDepth || is_int($maxDepth)) {
            $this->_maxDepth = $maxDepth;
        } else {
            $this->_maxDepth = (int) $maxDepth;
        }
        return $this;
    }

    /**
     * Returns maximum depth a page can have to be included when rendering
     *
     * @return int|null  maximum depth or null
     */
    public function getMaxDepth()
    {
        return $this->_maxDepth;
    }

    /**
     * Set the indentation string for using in {@link render()}, optionally a
     * number of spaces to indent with
     *
     * @param  string|int $indent                          indentation string or
     *                                                     number of spaces
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setIndent($indent)
    {
        $this->_indent = $this->_getWhitespace($indent);
        return $this;
    }

    /**
     * Returns indentation
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->_indent;
    }

    /**
     * Sets translator to use in helper
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::setTranslator()}.
     *
     * @param  mixed $translator                           [optional] translator.
     *                                                     Expects an object of
     *                                                     type
     *                                                     {@link Zend_Translate_Adapter}
     *                                                     or {@link Zend_Translate},
     *                                                     or null. Default is
     *                                                     null, which sets no
     *                                                     translator.
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setTranslator($translator = null)
    {
        if (null == $translator ||
            $translator instanceof Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        }

        return $this;
    }

    /**
     * Returns translator used in helper
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::getTranslator()}.
     *
     * @return Zend_Translate_Adapter|null  translator or null
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $this->setTranslator(Zend_Registry::get('Zend_Translate'));
            }
        }

        return $this->_translator;
    }

    /**
     * Sets ACL to use when iterating pages
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::setAcl()}.
     *
     * @param  Zend_Acl $acl                               [optional] ACL object.
     *                                                     Default is null.
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setAcl(Zend_Acl $acl = null)
    {
        $this->_acl = $acl;
        return $this;
    }

    /**
     * Returns ACL or null if it isn't set using {@link setAcl()} or
     * {@link setDefaultAcl()}
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::getAcl()}.
     *
     * @return Zend_Acl|null  ACL object or null
     */
    public function getAcl()
    {
        if ($this->_acl === null && self::$_defaultAcl !== null) {
            return self::$_defaultAcl;
        }

        return $this->_acl;
    }

    /**
     * Sets ACL role(s) to use when iterating pages
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::setRole()}.
     *
     * @param  mixed $role                                 [optional] role to
     *                                                     set. Expects a string,
     *                                                     an instance of type
     *                                                     {@link Zend_Acl_Role_Interface},
     *                                                     or null. Default is
     *                                                     null, which will set
     *                                                     no role.
     * @throws Zend_View_Exception                         if $role is invalid
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setRole($role = null)
    {
        if (null === $role || is_string($role) ||
            $role instanceof Zend_Acl_Role_Interface) {
            $this->_role = $role;
        } else {
            require_once 'Zend/View/Exception.php';
            $e = new Zend_View_Exception(sprintf(
                '$role must be a string, null, or an instance of '
                .  'Zend_Acl_Role_Interface; %s given',
                gettype($role)
            ));
            $e->setView($this->view);
            throw $e;
        }

        return $this;
    }

    /**
     * Returns ACL role to use when iterating pages, or null if it isn't set
     * using {@link setRole()} or {@link setDefaultRole()}
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::getRole()}.
     *
     * @return string|Zend_Acl_Role_Interface|null  role or null
     */
    public function getRole()
    {
        if ($this->_role === null && self::$_defaultRole !== null) {
            return self::$_defaultRole;
        }

        return $this->_role;
    }

    /**
     * Sets whether ACL should be used
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::setUseAcl()}.
     *
     * @param  bool $useAcl                                [optional] whether ACL
     *                                                     should be used.
     *                                                     Default is true.
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setUseAcl($useAcl = true)
    {
        $this->_useAcl = (bool) $useAcl;
        return $this;
    }

    /**
     * Returns whether ACL should be used
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::getUseAcl()}.
     *
     * @return bool  whether ACL should be used
     */
    public function getUseAcl()
    {
        return $this->_useAcl;
    }

    /**
     * Return renderInvisible flag
     *
     * @return bool
     */
    public function getRenderInvisible()
    {
        return $this->_renderInvisible;
    }

    /**
     * Render invisible items?
     *
     * @param  bool $renderInvisible                       [optional] boolean flag
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface
     *                                                     returns self
     */
    public function setRenderInvisible($renderInvisible = true)
    {
        $this->_renderInvisible = (bool) $renderInvisible;
        return $this;
    }

    /**
     * Sets whether translator should be used
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::setUseTranslator()}.
     *
     * @param  bool $useTranslator                         [optional] whether
     *                                                     translator should be
     *                                                     used. Default is true.
     * @return Zend_View_Helper_Navigation_HelperAbstract  fluent interface,
     *                                                     returns self
     */
    public function setUseTranslator($useTranslator = true)
    {
        $this->_useTranslator = (bool) $useTranslator;
        return $this;
    }

    /**
     * Returns whether translator should be used
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::getUseTranslator()}.
     *
     * @return bool  whether translator should be used
     */
    public function getUseTranslator()
    {
        return $this->_useTranslator;
    }

    // Magic overloads:


   

    // Public methods:

    /**
     * Checks if the helper has a container
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::hasContainer()}.
     *
     * @return bool  whether the helper has a container or not
     */
    public function hasContainer()
    {
        return null !== self::$_container;
    }

    /**
     * Checks if the helper has an ACL instance
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::hasAcl()}.
     *
     * @return bool  whether the helper has a an ACL instance or not
     */
    public function hasAcl()
    {
        return null !== $this->_acl;
    }

    /**
     * Checks if the helper has an ACL role
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::hasRole()}.
     *
     * @return bool  whether the helper has a an ACL role or not
     */
    public function hasRole()
    {
        return null !== $this->_role;
    }

    /**
     * Checks if the helper has a translator
     *
     * Implements {@link Zend_View_Helper_Navigation_Helper::hasTranslator()}.
     *
     * @return bool  whether the helper has a translator or not
     */
    public function hasTranslator()
    {
        return null !== $this->_translator;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page
     *
     * @param  Zend_Navigation_Page $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function htmlify(Zend_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for anchor element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass(),
            'href'   => $page->getHref(),
            'target' => $page->getTarget()
        );

        return '<a' . $this->_htmlAttribs($attribs) . '>'
             . $this->view->escape($label)
             . '</a>';
    }

    // Iterator filter methods:

    /**
     * Determines whether a page should be accepted when iterating
     *
     * Rules:
     * - If a page is not visible it is not accepted, unless RenderInvisible has
     *   been set to true.
     * - If helper has no ACL, page is accepted
     * - If helper has ACL, but no role, page is not accepted
     * - If helper has ACL and role:
     *  - Page is accepted if it has no resource or privilege
     *  - Page is accepted if ACL allows page's resource or privilege
     * - If page is accepted by the rules above and $recursive is true, the page
     *   will not be accepted if it is the descendant of a non-accepted page.
     *
     * @param  Zend_Navigation_Page $page      page to check
     * @param  bool                $recursive  [optional] if true, page will not
     *                                         be accepted if it is the
     *                                         descendant of a page that is not
     *                                         accepted. Default is true.
     * @return bool                            whether page should be accepted
     */
    public function accept(Zend_Navigation_Page $page, $recursive = true)
    {
        // accept by default
        $accept = true;

        if (!$page->isVisible(false) && !$this->getRenderInvisible()) {
            // don't accept invisible pages
            $accept = false;
        } elseif ($this->getUseAcl() && !$this->_acceptAcl($page)) {
            // acl is not amused
            $accept = false;
        }

        if ($accept && $recursive) {
            $parent = $page->getParent();
            if ($parent instanceof Zend_Navigation_Page) {
                $accept = $this->accept($parent, true);
            }
        }

        return $accept;
    }

    /**
     * Determines whether a page should be accepted by ACL when iterating
     *
     * Rules:
     * - If helper has no ACL, page is accepted
     * - If page has a resource or privilege defined, page is accepted
     *   if the ACL allows access to it using the helper's role
     * - If page has no resource or privilege, page is accepted
     *
     * @param  Zend_Navigation_Page $page  page to check
     * @return bool                        whether page is accepted by ACL
     */
    protected function _acceptAcl(Zend_Navigation_Page $page)
    {
        if (!$acl = $this->getAcl()) {
            // no acl registered means don't use acl
            return true;
        }

        $role = $this->getRole();
        $resource = $page->getResource();
        $privilege = $page->getPrivilege();

        if ($resource || $privilege) {
            // determine using helper role and page resource/privilege
            return $acl->isAllowed($role, $resource, $privilege);
        }

        return true;
    }

    // Util methods:

    /**
     * Retrieve whitespace representation of $indent
     *
     * @param  int|string $indent
     * @return string
     */
    protected function _getWhitespace($indent)
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }

        return (string) $indent;
    }

    /**
     * Converts an associative array to a string of tag attributes.
     *
     * Overloads {@link Zend_View_Helper_HtmlElement::_htmlAttribs()}.
     *
     * @param  array $attribs  an array where each key-value pair is converted
     *                         to an attribute name and value
     * @return string          an attribute string
     */
    protected function _htmlAttribs($attribs)
    {
        // filter out null values and empty string values
        foreach ($attribs as $key => $value) {
            if ($value === null || (is_string($value) && !strlen($value))) {
                unset($attribs[$key]);
            }
        }


        $xhtml = '';
        foreach ((array) $attribs as $key => $val) {
            $key = $this->view->escape($key);

            if (('on' == substr($key, 0, 2)) || ('constraints' == $key)) {
                // Don't escape event attributes; _do_ substitute double quotes with singles
                if (!is_scalar($val)) {
                    // non-scalar data should be cast to JSON first
                    require_once 'Zend/Json.php';
                    $val = Zend_Json::encode($val);
                }
                $val = preg_replace('/"([^"]*)":/', '$1:', $val);
            } else {
                if (is_array($val)) {
                    $val = implode(' ', $val);
                }
                $val = $this->view->escape($val);
            }

            if ('id' == $key) {
                $val = $this->_normalizeId($val);
            }

            if (strpos($val, '"') !== false) {
                $xhtml .= " $key='$val'";
            } else {
                $xhtml .= " $key=\"$val\"";
            }

        }
        return $xhtml;
    }

    

    /**
     * Normalize an ID
     *
     * Overrides {@link Zend_View_Helper_HtmlElement::_normalizeId()}.
     *
     * @param  string $value
     * @return string
     */
    protected function _normalizeId($value)
    {
        $prefix = get_class($this);
        $prefix = strtolower(trim(substr($prefix, strrpos($prefix, '_')), '_'));

        return $prefix . '-' . $value;
    }

    // Static methods:

    /**
     * Sets default ACL to use if another ACL is not explicitly set
     *
     * @param  Zend_Acl $acl  [optional] ACL object. Default is null, which
     *                        sets no ACL object.
     * @return void
     */
    public static function setDefaultAcl(Zend_Acl $acl = null)
    {
        self::$_defaultAcl = $acl;
    }

    /**
     * Sets default ACL role(s) to use when iterating pages if not explicitly
     * set later with {@link setRole()}
     *
     * @param  midex $role               [optional] role to set. Expects null,
     *                                   string, or an instance of
     *                                   {@link Zend_Acl_Role_Interface}.
     *                                   Default is null, which sets no default
     *                                   role.
     * @throws Zend_View_Exception       if role is invalid
     * @return void
     */
    public static function setDefaultRole($role = null)
    {
        if (null === $role ||
            is_string($role) ||
            $role instanceof Zend_Acl_Role_Interface) {
            self::$_defaultRole = $role;
        } else {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(
                '$role must be null|string|Zend_Acl_Role_Interface'
            );
        }
    }
    
}