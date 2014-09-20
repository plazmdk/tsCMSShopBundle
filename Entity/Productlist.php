<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 5/18/14
 * Time: 9:44 PM
 */

namespace tsCMS\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use tsCMS\ShopBundle\Interfaces\PriceInterface;
use tsCMS\SystemBundle\Validator\Constraints as tsCMSConstraints;
use tsCMS\SystemBundle\Interfaces\PathInterface;

/**
 * @ORM\Table(name="productlist")
 * @ORM\Entity
 * @tsCMSConstraints\Path()
 */
class Productlist implements PathInterface {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;
    /**
     * @ORM\OneToMany(targetEntity="ProductlistProduct", mappedBy="productlist", cascade={"persist","remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $singleProducts;
    /**
     * @ORM\ManyToMany(targetEntity="Category")
     * @ORM\JoinTable(name="productlist_categories",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    protected $categories;

    protected $path;

    function __construct()
    {
        $this->singleProducts = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }


    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param ProductlistProduct $singleProduct
     */
    public function addSingleProduct($singleProduct)
    {
        $this->singleProducts->add($singleProduct);
        $singleProduct->setProductlist($this);
        $singleProduct->setPosition(count($this->singleProducts));
    }

    /**
     * @param ProductlistProduct $singleProduct
     */
    public function removeSingleProduct($singleProduct)
    {
        $this->singleProducts->removeElement($singleProduct);
        $singleProduct->setProductlist(null);
    }

    /**
     * @return ProductlistProduct[]|PersistentCollection
     */
    public function getSingleProducts()
    {
        return $this->singleProducts;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return Category[]|PersistentCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     */
    public function addCategory($category) {
        if ($this->categories->contains($category)) return;
        $this->categories->add($category);
    }

    /**
     * @param Category $category
     */
    public function removeCategory($category) {
        $this->categories->removeElement($category);
    }

}