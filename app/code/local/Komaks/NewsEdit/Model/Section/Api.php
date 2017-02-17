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
class Komaks_NewsEdit_Model_Section_Api extends Mage_Api_Model_Resource_Abstract
{
    protected $_defaultAttributeList = array(
        'name', 
        'description', 
        'status', 
        'url_key', 
        'parent_id', 
        'path', 
        'position', 
        'level', 
        'children_count', 
        'in_rss', 
        'meta_title', 
        'meta_keywords', 
        'meta_description', 
        'allow_comment', 
        'created_at', 
        'updated_at', 
    );


    /**
     * init section
     *
     * @access protected
     * @param $sectionId
     * @return Komaks_NewsEdit_Model_Section
     * @author      Ultimate Module Creator
     */
    protected function _initSection($sectionId)
    {
        $section = Mage::getModel('komaks_newsedit/section')->load($sectionId);
        if (!$section->getId()) {
            $this->_fault('section_not_exists');
        }
        return $section;
    }

    /**
     * get sections
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Ultimate Module Creator
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('komaks_newsedit/section')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'entity_id',
                array(
                    'neq'=>Mage::helper('komaks_newsedit/section')->getRootSectionId()
                )
            );
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $section) {
            $result[] = $this->_getApiData($section);
        }
        return $result;
    }

    /**
     * Add section
     *
     * @access public
     * @param array $data
     * @return array
     * @author Ultimate Module Creator
     */
    public function add($data)
    {
        try {
            if (is_null($data)) {
                throw new Exception(Mage::helper('komaks_newsedit')->__("Data cannot be null"));
            }
            $data = (array)$data;
            if (isset($data['additional_attributes']) && is_array($data['additional_attributes'])) {
                foreach ($data['additional_attributes'] as $key=>$value) {
                    $data[$key] = $value;
                }
                unset($data['additional_attributes']);
            }
            $data['attribute_set_id'] = Mage::getModel('komaks_newsedit/section')->getDefaultAttributeSetId();
            $section = Mage::getModel('komaks_newsedit/section')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $section->getId();
    }

    /**
     * Change existing section information
     *
     * @access public
     * @param int $sectionId
     * @param array $data
     * @return bool
     * @author Ultimate Module Creator
     */
    public function update($sectionId, $data)
    {
        $section = $this->_initSection($sectionId);
        try {
            $data = (array)$data;
            if (isset($data['additional_attributes']) && is_array($data['additional_attributes'])) {
                foreach ($data['additional_attributes'] as $key=>$value) {
                    $data[$key] = $value;
                }
                unset($data['additional_attributes']);
            }
            $section->addData($data);
            $section->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove section
     *
     * @access public
     * @param int $sectionId
     * @return bool
     * @author Ultimate Module Creator
     */
    public function remove($sectionId)
    {
        $section = $this->_initSection($sectionId);
        try {
            $section->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $sectionId
     * @return array
     * @author Ultimate Module Creator
     */
    public function info($sectionId)
    {
        $result = array();
        $section = $this->_initSection($sectionId);
        $result = $this->_getApiData($section);
        //related products
        $result['products'] = array();
        $relatedProductsCollection = $section->getSelectedProductsCollection();
        foreach ($relatedProductsCollection as $product) {
            $result['products'][$product->getId()] = $product->getPosition();
        }
        //related categories
        $result['categories'] = array();
        $relatedCategoriesCollection = $section->getSelectedCategoriesCollection();
        foreach ($relatedCategoriesCollection as $category) {
            $result['categories'][$category->getId()] = $category->getPosition();
        }
        //related articles
        $result['articles'] = array();
        $relatedArticlesCollection = $section->getSelectedArticlesCollection();
        foreach ($relatedArticlesCollection as $article) {
            $result['articles'][$article->getId()] = $article->getPosition();
        }
        return $result;
    }

    /**
     * Move section in tree
     *
     * @param int $sectionId
     * @param int $parentId
     * @param int $afterId
     * @return boolean
     */
    public function move($sectionId, $parentId, $afterId = null)
    {
        $section = $this->_initSection($sectionId);
        $parentSection = $this->_initSection($parentId);
        if ($afterId === null && $parentSection->hasChildren()) {
            $parentChildren = $parentSection->getChildSections();
            $afterId = array_pop(explode(',', $parentChildren));
        }
        if ( strpos($parentSection->getPath(), $section->getPath()) === 0) {
            $this->_fault(
                'not_moved',
                Mage::helper('komaks_newsedit')->__("Cannot move parent inside section")
            );
        }
        try {
            $section->move($parentId, $afterId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_moved', $e->getMessage());
        }
        return true;
    }
    /**
     * Assign product to section
     *
     * @access public
     * @param int $sectionId
     * @param int $productId
     * @param int $position
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignProduct($sectionId, $productId, $position = null)
    {
        $section = $this->_initSection($sectionId);
        $positions    = array();
        $products     = $section->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }
        $positions[$productId]['position'] = $position;
        $section->setProductsData($positions);
        try {
            $section->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove product from section
     *
     * @access public
     * @param int $sectionId
     * @param int $productId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignProduct($sectionId, $productId)
    {
        $section = $this->_initSection($sectionId);
        $positions    = array();
        $products     = $section->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        unset($positions[$productId]);
        $section->setProductsData($positions);
        try {
            $section->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign category to section
     *
     * @access public
     * @param int $sectionId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignCategory($sectionId, $categoryId)
    {
        $section = $this->_initSection($sectionId);
        $category   = Mage::getModel('catalog/category')->load($categoryId);
        if (!$category->getId()) {
            $this->_fault('category_not_exists');
        }
        $categories = $section->getSelectedCategories();
        $categoryIds = array();
        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }
        $categoryIds[] = $categoryId;
        $section->setCategoriesData($categoryIds);
        try {
            $section->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove category from section
     *
     * @access public
     * @param int $sectionId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignCategory($sectionId, $categoryId)
    {
        $section    = $this->_initSection($sectionId);
        $categories    = $section->getSelectedCategories();
        $categoryIds   = array();
        foreach ($categories as $key=>$category) {
            if ($category->getId() != $categoryId) {
                $categoryIds[] = $category->getId();
            }
        }
        $section->setCategoriesData($categoryIds);
        try {
            $section->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign article to section
     *
     * @access public
     * @param int $sectionId
     * @param int $articleId
     * @param int $position
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignArticle($sectionId, $articleId, $position = null)
    {
        $section = $this->_initSection($sectionId);
        $positions    = array();
        $articles     = $section->getSelectedArticles();
        foreach ($articles as $article) {
            $articles[$article->getId()] = array('position'=>$article->getPosition());
        }
        $article = Mage::getModel('komaks_newsedit/article')->load($articleId);
        if (!$article->getId()) {
            $this->_fault('section_article_not_exists');
        }
        $positions[$articleId]['position'] = $position;
        $article->setArticlesData($positions);
        try {
            $article->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove article from section
     *
     * @access public
     * @param int $sectionId
     * @param int $articleId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignArticle($sectionId, $articleId)
    {
        $section = $this->_initSection($sectionId);
        $positions    = array();
        $articles     = $section->getSelectedArticles();
        foreach ($articles as $article) {
            $articles[$article->getId()] = array('position'=>$article->getPosition());
        }
        unset($positions[$articleId]);
        $section->setArticlesData($positions);
        try {
            $section->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Get list of additional attributes which are not in default create/update list
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAdditionalAttributes()
    {
        $entity = Mage::getModel('eav/entity_type')->load('komaks_newsedit_section', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId());
        $result = array();
        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $this->_defaultAttributeList)) {
                if ($attribute->getIsGlobal() == Komaks_NewsEdit_Model_Attribute::SCOPE_GLOBAL) {
                    $scope = 'global';
                } elseif ($attribute->getIsGlobal() == Komaks_NewsEdit_Model_Attribute::SCOPE_WEBSITE) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code'         => $attribute->getAttributeCode(),
                    'type'         => $attribute->getFrontendInput(),
                    'required'     => $attribute->getIsRequired(),
                    'scope'        => $scope
                );
            }
        }

        return $result;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Section $section
     * @return array()
     * @author Ultimate Module Creator
     */
    protected function _getApiData(Komaks_NewsEdit_Model_Section $section)
    {
        $data = array();
        $additional = array();
        $additionalAttributes = $this->getAdditionalAttributes();
        $additionalByCode = array();
        foreach ($additionalAttributes as $attribute) {
            $additionalByCode[] = $attribute['code'];
        }
        foreach ($section->getData() as $key=>$value) {
            if (!in_array($key, $additionalByCode)) {
                $data[$key] = $value;
            } else {
                $additional[] = array('key'=>$key, 'value'=>$value);
            }
        }
        if (!empty($additional)) {
            $data['additional_attributes'] = $additional;
        }
        return $data;
    }
}
