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
 * Article admin edit tab attributes block
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
*/
class Komaks_NewsEdit_Block_Adminhtml_Article_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
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
        $form->setDataObject(Mage::registry('current_article'));
        $fieldset = $form->addFieldset(
            'info',
            array(
                'legend' => Mage::helper('komaks_newsedit')->__('Article Information'),
                'class' => 'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('komaks_newsedit/article'));
        }
        $this->_setFieldset($attributes, $fieldset, array());
        $formValues = Mage::registry('current_article')->getData();
        if (!Mage::registry('current_article')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }
        $form->addValues($formValues);
        $form->setFieldNameSuffix('article');
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
                'komaks_newsedit/adminhtml_article_helper_file'
            ),
            'image'    => Mage::getConfig()->getBlockClassName(
                'komaks_newsedit/adminhtml_article_helper_image'
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
     * @return Komaks_NewsEdit_Model_Article
     * @author Ultimate Module Creator
     */
    public function getArticle()
    {
        return Mage::registry('current_article');
    }

    /**
     * get after element html
     *
     * @access protected
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     * @author Ultimate Module Creator
     */
    protected function _getAdditionalElementHtml($element)
    {
        if ($element->getName() == 'author_id') {
            $html = '<a href="{#url}" id="author_id_link" target="_blank"></a>';
            $html .= '<script type="text/javascript">
            function changeAuthorIdLink() {
                if ($(\'author_id\').value == \'\') {
                    $(\'author_id_link\').hide();
                } else {
                    $(\'author_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/newsedit_author/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'author_id\').value);
                    $(\'author_id_link\').href = realUrl;
                    $(\'author_id_link\').innerHTML = text.replace(\'{#name}\', $(\'author_id\').options[$(\'author_id\').selectedIndex].innerHTML);
                }
            }
            $(\'author_id\').observe(\'change\', changeAuthorIdLink);
            changeAuthorIdLink();
            </script>';
            return $html;
        }
        return '';
    }
}
