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
 * Author edit form tab
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Author_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Author_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('author_');
        $form->setFieldNameSuffix('author');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'author_form',
            array('legend' => Mage::helper('komaks_newsedit')->__('Author'))
        );
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('komaks_newsedit')->__('Name'),
                'name'  => 'name',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'description',
            'editor',
            array(
                'label' => Mage::helper('komaks_newsedit')->__('Description'),
                'name'  => 'description',
            'config' => $wysiwygConfig,
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'email',
            'text',
            array(
                'label' => Mage::helper('komaks_newsedit')->__('Email'),
                'name'  => 'email',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'url_key',
            'text',
            array(
                'label' => Mage::helper('komaks_newsedit')->__('Url key'),
                'name'  => 'url_key',
                'note'  => Mage::helper('komaks_newsedit')->__('Relative to Website Base URL')
            )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('komaks_newsedit')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('komaks_newsedit')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('komaks_newsedit')->__('Disabled'),
                    ),
                ),
            )
        );
        $fieldset->addField(
            'in_rss',
            'select',
            array(
                'label'  => Mage::helper('komaks_newsedit')->__('Show in rss'),
                'name'   => 'in_rss',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('komaks_newsedit')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('komaks_newsedit')->__('No'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_author')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getAuthorData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getAuthorData());
            Mage::getSingleton('adminhtml/session')->setAuthorData(null);
        } elseif (Mage::registry('current_author')) {
            $formValues = array_merge($formValues, Mage::registry('current_author')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
