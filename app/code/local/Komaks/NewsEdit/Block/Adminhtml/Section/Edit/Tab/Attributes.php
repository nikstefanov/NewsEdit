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
 * Section admin edit tab attributes block
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
*/
class Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the attributes for the form
     *
     * @access protected
     * @return void
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_section'));
        $fieldset = $form->addFieldset(
            'info',
            array(
                'legend' => Mage::helper('komaks_newsedit')->__('Section Information'),
                'class' => 'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('komaks_newsedit/section'));
        }
        if ($this->getAddHiddenFields()) {
            if (!$this->getSection()->getId()) {
                // path
                if ($this->getRequest()->getParam('parent')) {
                    $fieldset->addField(
                        'path',
                        'hidden',
                        array(
                            'name'  => 'path',
                            'value' => $this->getRequest()->getParam('parent')
                        )
                    );
                } else {
                    $fieldset->addField(
                        'path',
                        'hidden',
                        array(
                            'name'  => 'path',
                            'value' => 1
                        )
                    );
                }
            } else {
                $fieldset->addField(
                    'id',
                    'hidden',
                    array(
                        'name'  => 'id',
                        'value' => $this->getSection()->getId()
                    )
                );
                $fieldset->addField(
                    'path',
                    'hidden',
                    array(
                        'name'  => 'path',
                        'value' => $this->getSection()->getPath()
                    )
                );
            }
        }
        $this->_setFieldset($attributes, $fieldset, array());
        $formValues = Mage::registry('current_section')->getData();
        if (!Mage::registry('current_section')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }
        //do not set default value for path
        unset($formValues['path']);
        $form->addValues($formValues);
        $form->setFieldNameSuffix('section');
        $this->setForm($form);
    }

    /**
     * prepare layout
     *
     * @access protected
     * @return void
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareLayout()
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('komaks_newsedit/adminhtml_newsedit_renderer_fieldset_element')
        );
    }

    /**
     * get the additional element types for form
     *
     * @access protected
     * @return array()
     * @see Mage_Adminhtml_Block_Widget_Form::_getAdditionalElementTypes()
     * @author Ultimate Module Creator
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'     => Mage::getConfig()->getBlockClassName(
                'komaks_newsedit/adminhtml_section_helper_file'
            ),
            'image'    => Mage::getConfig()->getBlockClassName(
                'komaks_newsedit/adminhtml_section_helper_image'
            ),
            'textarea' => Mage::getConfig()->getBlockClassName(
                'adminhtml/catalog_helper_form_wysiwyg'
            )
        );
    }

    /**
     * get current entity
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function getSection()
    {
        return Mage::registry('current_section');
    }
}
