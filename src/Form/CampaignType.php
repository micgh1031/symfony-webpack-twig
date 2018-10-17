<?php

namespace App\Form;

use App\Entity\Campaign;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $partnerArray = $options['partnerArray'];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ])
            ->add('partner_id', ChoiceType::class, [
                'required' => true,
                'trim' => true,
                'choices' => array_flip($partnerArray),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ])
            ->add('start_at', DateTimeType::class, [
                'required' => false,
                'trim' => true,
                'date_widget' => 'single_text',
                'input' => 'timestamp',
                'attr' => [
                    'type' => 'datetime-local',
                ],
            ])
            ->add('end_at', DateTimeType::class, [
                'required' => false,
                'trim' => true,
                'date_widget' => 'single_text',
                'input' => 'timestamp',
                'attr' => [
                    'type' => 'datetime-local',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (short: 1 or 2 lines)',
                'required' => false,
                'trim' => true,
                'attr' => [
                    'rows' => 5,
                ],
            ])
            ->add('details', TextareaType::class, [
                'label' => 'Details',
                'required' => false,
                'trim' => true,
                'attr' => [
                    'rows' => 10,
                ],
            ])
            ->add('benefits', TextareaType::class, [
                'label' => 'Benefits (Short bullet list)',
                'required' => false,
                'trim' => true,
                'attr' => [
                    'rows' => 5,
                ],
            ])
            ->add('image_url', UrlType::class, [
                'required' => false,
                'trim' => true,
                'constraints' => array(
                    new Assert\Url(),
                ),
            ])
            ->add('image_urls', TextareaType::class, [
                'required' => false,
                'trim' => true,
                'label' => 'Secondary image urls (separate by new line)',
                'attr' => [
                    'rows' => 5,
                ],
            ])

            ->add('shared_code', TextType::class, [
                'required' => false,
                'trim' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Campaign::class,
            'partnerArray' => 'partnerArray',
        ]);
    }
}
