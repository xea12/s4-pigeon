<?php
// src/Form/CampaignType.php
namespace App\Form;

use App\Entity\Campaign;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nazwa', TextType::class)
            ->add('temat', TextType::class)
            ->add('nazwa_nadawcy', TextType::class)
            ->add('email_nadawcy', EmailType::class)
            ->add('data_wysylki', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    'class' => 'js-datepicker form-control',
                    'autocomplete' => 'off'
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Szkic' => 'Szkic',
                    'Zaplanowana' => 'Zaplanowana',
                    'Wysłana' => 'Wysłana',
                    'Anulowana' => 'Anulowana',
                ],
            ])
            ->add('segment_product_type', ChoiceType::class, [
                'choices' => $options['segment_product_type_choices'],
                'mapped' => false,
                'required' => true,
            ])
            ->add('segment_otwierane', ChoiceType::class, [
                'choices' => $options['segment_otwierane_choices'],
                'mapped' => false,
                'required' => true,
            ])
            ->add('segment_firma', ChoiceType::class, [
                'choices' => $options['segment_firma_choices'],
                'mapped' => false,
                'required' => true,
            ])
            ->add('segment_czas', ChoiceType::class, [
                'choices' => $options['segment_czas_choices'],
                'mapped' => false,
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Campaign::class,
            'segment_product_type_choices' => [],
            'segment_otwierane_choices' => [],
            'segment_firma_choices' => [],
            'segment_czas_choices' => [],
        ]);
    }
}