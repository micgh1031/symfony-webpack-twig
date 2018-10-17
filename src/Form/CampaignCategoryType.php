<?php

namespace App\Form;

use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignCategoryType extends AbstractType
{
    protected $categoryArray = [];

    public function __construct(CategoryRepository $categoryRepository)
    {
        foreach ($categoryRepository->findAll() as $category) {
            $this->categoryArray[$category->getId()] = $category->getName();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $campaignCategoryArray = $options['campaignCategoryArray'];
        $builder
            ->add('categories', ChoiceType::class, [
                'required' => false,
                'trim' => true,
                'multiple' => true,
                'expanded' => true,
                'choices' => array_flip($this->categoryArray),
                'data' => $campaignCategoryArray,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'campaignCategoryArray' => 'campaignCategoryArray',
        ]);
    }
}
