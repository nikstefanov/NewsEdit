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
 * Article admin grid block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Article_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('articleGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('komaks_newsedit/article')
            ->getCollection()
            ->addAttributeToSelect('author_id')
            ->addAttributeToSelect('publication_date')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('url_key');
        
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();
        $collection->joinAttribute(
            'title', 
            'komaks_newsedit_article/title', 
            'entity_id', 
            null, 
            'inner', 
            $adminStore
        );
        if ($store->getId()) {
            $collection->joinAttribute(
                'komaks_newsedit_article_title', 
                'komaks_newsedit_article/title', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('komaks_newsedit')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'author_id',
            array(
                'header'    => Mage::helper('komaks_newsedit')->__('Author'),
                'index'     => 'author_id',
                'type'      => 'options',
                'options'   => Mage::getResourceModel('komaks_newsedit/author_collection')
                    ->toOptionHash(),
                'renderer'  => 'komaks_newsedit/adminhtml_helper_column_renderer_parent',
                'params'    => array(
                    'id'    => 'getAuthorId'
                ),
                'base_link' => 'adminhtml/newsedit_author/edit'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('komaks_newsedit')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );
        
        if ($this->_getStore()->getId()) {
            $this->addColumn(
                'komaks_newsedit_article_title', 
                array(
                    'header'    => Mage::helper('komaks_newsedit')->__('Title in %s', $this->_getStore()->getName()),
                    'align'     => 'left',
                    'index'     => 'komaks_newsedit_article_title',
                )
            );
        }

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('komaks_newsedit')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('komaks_newsedit')->__('Enabled'),
                    '0' => Mage::helper('komaks_newsedit')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'publication_date',
            array(
                'header' => Mage::helper('komaks_newsedit')->__('Publication date'),
                'index'  => 'publication_date',
                'type'=> 'date',

            )
        );
        $this->addColumn(
            'url_key',
            array(
                'header' => Mage::helper('komaks_newsedit')->__('URL key'),
                'index'  => 'url_key',
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('komaks_newsedit')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('komaks_newsedit')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('komaks_newsedit')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('komaks_newsedit')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('komaks_newsedit')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('komaks_newsedit')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('komaks_newsedit')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * get the selected store
     *
     * @access protected
     * @return Mage_Core_Model_Store
     * @author Ultimate Module Creator
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('article');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('komaks_newsedit')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('komaks_newsedit')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('komaks_newsedit')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('komaks_newsedit')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('komaks_newsedit')->__('Enabled'),
                            '0' => Mage::helper('komaks_newsedit')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $values = Mage::getResourceModel('komaks_newsedit/author_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem(
            'author_id',
            array(
                'label'      => Mage::helper('komaks_newsedit')->__('Change Author'),
                'url'        => $this->getUrl('*/*/massAuthorId', array('_current'=>true)),
                'additional' => array(
                    'flag_author_id' => array(
                        'name'   => 'flag_author_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('komaks_newsedit')->__('Author'),
                        'values' => $values
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Article
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
