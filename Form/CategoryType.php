<?php

namespace tsCMS\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use tsCMS\ShopBundle\Entity\Category;

class CategoryType extends AbstractType
{
    /** @var Category */
    private $category;

    public function __construct($category = null) {
        $this->category = $category;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $category = $this->category;
        $builder
            ->add('title','text', array(
                'label' => 'category.title',
                'required' => true
            ))
            ->add("parent", "entity", array(
                "label" => "category.parent",
                'class'    => 'tsCMSShopBundle:Category',
                'required' => false,
                'query_builder' => function(EntityRepository $er) use($category)
                    {
                        $qb = $er->createQueryBuilder('m')
                            ->orderBy('m.lft');

                        if ($category) {
                            $qb
                                ->andWhere(new Orx(array('m.lft < :lft','m.rgt > :rgt')))
                                ->setParameter('lft', $category->getLft())
                                ->setParameter("rgt", $category->getRgt());
                        }
                        return $qb;
                    },
            ))
            ->add("save","submit",array(
                'label' => 'category.save',
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tscms_shopbundle_category';
    }
}
