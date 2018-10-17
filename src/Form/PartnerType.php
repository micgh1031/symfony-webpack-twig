<?php

namespace App\Form;

use App\Entity\Partner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('display_name', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'trim' => true,
                'choices' => ['active' => 'Active', 'archive' => 'Inactive'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('image_url', UrlType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('website', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('addressline1', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('addressline2', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('postalcode', TextType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'trim' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
        ]);
    }
}
