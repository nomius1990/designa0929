<?php
namespace Silk\Theme\Block;

use Magento\Catalog\Model\Category;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\View\Element\Template;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu {

    protected  $_category;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected  $_directoryList;
    /**
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        array $data = [],
        Category $category,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->_directoryList = $directoryList;
        $this->_category = $category;
        parent::__construct($context, $nodeFactory,$treeFactory,$data);
    }

    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit,$childLeveOne='')
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }
        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }
        if ($child->getName() == 'Shop') {
            $newModel = $child;
        }
        $html .= '<div class="' . $childrenWrapClass . '">';
        $html .=    '<ul class="level">';

        $result = $this->_getHtml($child, $childrenWrapClass, $limit, $colStops,$childLeveOne);
        if (is_array($result) && !empty($result)) {
            if ($childLeveOne) {
                $third_html = '';
                for($i=0;$i<count($result);$i++){
                    $src = $result[$i]['src'];
                    if ($i%$childLeveOne == 0) {
                        $third_html .= '<li '.$result[$i]['class'].'><p><a href='.$result[$i]['url'] . ' class="color" data-src='.$src.'><span>' . $this->escapeHtml($result[$i]['name']) . '</span></a></p>';
                        $third_html .= '</li>';
                    } else if ($i%$childLeveOne == ($childLeveOne-1)) {
                        $third_html .= '<li '.$result[$i]['class'].'><p><a href='.$result[$i]['url'] . ' class="color" data-src='.$src.'><span>' . $this->escapeHtml($result[$i]['name']) . '</span></a></p></li></ul><ul class="level">';
                    } else {
                        $third_html .= '<li '.$result[$i]['class'].'><p><a href='.$result[$i]['url'] . ' class="color" data-src='.$src.'><span>' . $this->escapeHtml($result[$i]['name']) . '</span></a></p></li>';
                    }
                }
                $html .= $third_html;
            }
        } else {
            $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops,$childLeveOne);
        }

        $media = $this->_directoryList->getUrlPath('media');
        if (isset($newModel)) {
            $id  = $newModel->getId();
            $arr = explode('-', $id);
            $categoryModel = $this->_category->load(intval($arr[2]));

            $html .= '<li class="thumbnail">';
            if ($categoryModel->getIsShow()) {
                $html .= '<img data-src="' . $this->getUrl('/') . $media . '/catalog/category/'.$categoryModel->getNavagationImage().'" data-init="' . $this->getUrl('/') . $media . '/catalog/category/'.$categoryModel->getNavagationImage().'" class="lazy" />';
            }
            $html .= '</li>';
        }
        $html .=    '</ul>';
        $html .= '</div>';

        return $html;
    }

    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = [],$childLeveOne=''
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;
        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();
        if ($childLevel == 1) {
            $childLeveOne = $childrenCount;
        } else {
            if (isset($childLeveOne) && !empty($childLeveOne)) {

            } else {
                $childLeveOne = 0;
            }
        }
        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';
        $result = [];
        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                continue;
            }
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);
            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            if ($childLevel == 2) {
                $info = [];
                $info['class']= $this->_getRenderedMenuItemAttributes($child);
                $info['url']  = $child->getUrl();
                $info['name'] = $child->getName();
                $info['src']  = $this->getImageInfo($child);
                $result[]     = $info;
            } else {
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';

                $html .= '<p ' . $outermostClassCode . '>';
                if ($child->getLevel() != 0) {
                    $src = $this->getImageInfo($child);
                } else {
                    $src = '';
                }

                $html .= '<a href="' . $child->getUrl() . '" class="color" data-src=' . $src . '><span>' . $this->escapeHtml($child->getName()) . '</span></a>';
                if ($child->hasChildren()) {
                    $html .= '<a href="javascript:;" class="link-toggle">';
                    $html .= '<svg class="icon top"><use xlink:href="#icon-minus"></use></svg>';
                    $html .= '<svg class="icon down"><use xlink:href="#icon-add"></use></svg>';
                    $html .= '</a>';
                }
                $html .= '</p>' . $this->_addSubMenu(
                        $child,
                        $childLevel,
                        $childrenWrapClass,
                        $limit,
                        $childLeveOne
                    ) . '</li>';


                $html .= '</li>';
            }
            $itemPosition++;
            $counter++;
        }
        if (!empty($result)) {
            return $result;
        } else {
            if (count($colBrakes) && $limit) {
                $html = '<li class="column"><ul>' . $html . '</ul></li>';
            }
            return $html;
        }
    }

    public function getImageInfo($child)
    {
        $media = $this->_directoryList->getUrlPath('media');
        $id  = $child->getId();
        $arr = explode('-', $id);
        $categoryModel = $this->_category->load(intval($arr[2]));
        if ($categoryModel->getIsShow()) {
            $results = empty($categoryModel->getNavagationImage())?'':$this->getUrl('/') . $media . '/catalog/category/'.$categoryModel->getNavagationImage();
        } else {
            $results = '';
        }
        return $results;
    }

    protected function _getMenuItemClasses(\Magento\Framework\Data\Tree\Node $item)
    {
        $classes = [];

        $classes[] = 'level' . $item->getLevel();

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        return $classes;
    }
}