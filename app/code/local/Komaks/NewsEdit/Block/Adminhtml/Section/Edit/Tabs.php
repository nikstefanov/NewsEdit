<?php
/**
 * Komaks_NewsEdit extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Komaks
 * @package        Komaks_NewsEdit
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Section admin edit tabs
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('section_info_tabs');
        $this->setDestElementId('section_tab_content');
        $this->setTitle(Mage::helper('komaks_newsedit')->__('Section Information'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        $section = $this->getSection();
        $entity = Mage::getModel('eav/entity_type')
            ->load('komaks_newsedit_section', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->addFieldToFilter(
            'attribute_code',
            array(
                'nin' => array('meta_title', 'meta_description', 'meta_keywords')
            )
        );
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'info',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Section Information'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_section_edit_tab_attributes'
                )
                ->setAttributes($attributes)
                ->setAddHiddenFields(true)
                ->toHtml(),
            )
        );
        $seoAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId())
            ->addFieldToFilter(
                'attribute_code',
                array(
                    'in' => array('meta_title', 'meta_description', 'meta_keywords')
                )
            );
        $seoAttributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'meta',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Meta'),
                'title'   => Mage::helper('komaks_newsedit')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_section_edit_tab_attributes'
                )
                ->setAttributes($seoAttributes)
                ->toHtml(),
            )
        );
        $this->addTab(
            'articles',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Articles'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_section_edit_tab_article',
                    'section.article.grid'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'products',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Associated Products'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_section_edit_tab_product',
                    'section.product.grid'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'categories',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Associated Categories'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_section_edit_tab_categories',
                    'section.category.tree'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve section entity
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function getSection()
    {
        return Mage::registry('current_section');
    }
}
