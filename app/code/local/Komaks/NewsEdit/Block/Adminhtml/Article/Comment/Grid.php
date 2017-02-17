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
 * Article comments admin grid block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Article_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('articleCommentGrid');
        $this->setDefaultSort('ct_comment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Comment_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('komaks_newsedit/article_comment_article_collection');
        $collection->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Comment_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'ct_comment_id',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Id'),
                'index'         => 'ct_comment_id',
                'type'          => 'number',
                'filter_index'  => 'ct.comment_id',
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Title'),
                'index'         => 'title',
                'filter_index'  => 'title',
            )
        );
        $this->addColumn(
            'ct_title',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Comment Title'),
                'index'         => 'ct_title',
                'filter_index'  => 'ct.title',
            )
        );
        $this->addColumn(
            'ct_name',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Poster name'),
                'index'         => 'ct_name',
                'filter_index'  => 'ct.name',
            )
        );
        $this->addColumn(
            'ct_email',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Poster email'),
                'index'         => 'ct_email',
                'filter_index'  => 'ct.email',
            )
        );
        $this->addColumn(
            'ct_status',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Status'),
                'index'         => 'ct_status',
                'filter_index'  => 'ct.status',
                'type'          => 'options',
                'options'       => array(
                    Komaks_NewsEdit_Model_Article_Comment::STATUS_PENDING  =>
                        Mage::helper('komaks_newsedit')->__('Pending'),
                    Komaks_NewsEdit_Model_Article_Comment::STATUS_APPROVED =>
                        Mage::helper('komaks_newsedit')->__('Approved'),
                    Komaks_NewsEdit_Model_Article_Comment::STATUS_REJECTED =>
                        Mage::helper('komaks_newsedit')->__('Rejected'),
                )
            )
        );
        $this->addColumn(
            'ct_created_at',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Created at'),
                'index'         => 'ct_created_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.created_at',
            )
        );
        $this->addColumn(
            'ct_updated_at',
            array(
                'header'        => Mage::helper('komaks_newsedit')->__('Updated at'),
                'index'         => 'ct_updated_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.updated_at',
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'stores',
                array(
                    'header'     => Mage::helper('komaks_newsedit')->__('Store Views'),
                    'index'      => 'stores',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback' => array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'action',
            array(
                'header'  => Mage::helper('komaks_newsedit')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getCtCommentId',
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
     * prepare mass action
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ct_comment_id');
        $this->setMassactionIdFilter('ct.comment_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('comment');
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
                'label' => Mage::helper('komaks_newsedit')->__('Change status'),
                'url'   => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                            'name' => 'status',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => Mage::helper('komaks_newsedit')->__('Status'),
                            'values' => array(
                                Komaks_NewsEdit_Model_Article_Comment::STATUS_PENDING  =>
                                    Mage::helper('komaks_newsedit')->__('Pending'),
                                Komaks_NewsEdit_Model_Article_Comment::STATUS_APPROVED =>
                                    Mage::helper('komaks_newsedit')->__('Approved'),
                                Komaks_NewsEdit_Model_Article_Comment::STATUS_REJECTED =>
                                    Mage::helper('komaks_newsedit')->__('Rejected'),
                            )
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
     * @param Komaks_NewsEdit_Model_Article_Comment
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getCtCommentId()));
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

    /**
     * filter store column
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Resource_Article_Comment_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Comment_Grid
     * @author Ultimate Module Creator
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->setStoreFilter($value);
        return $this;
    }
}
