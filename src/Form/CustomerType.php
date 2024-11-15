<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imie', TextType::class, [
                'label' => 'Imię',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('zakup', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('productId', TextType::class, [
                'label' => 'ID Produktu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('productNazwa', TextType::class, [
                'label' => 'Nazwa Produktu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('productCena', NumberType::class, [
                'label' => 'Cena Produktu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('productCenalow', NumberType::class, [
                'label' => 'Cena Niska Produktu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('ProductTyp', TextType::class, [
                'label' => 'Typ Produktu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('rabat', NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('Discount', NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('CustomersDaysFromOrder', NumberType::class, [
                'label' => 'Dni od zamówienia',
                'attr' => ['class' => 'form-control']
            ])
            ->add('CustomersOrdersCount', NumberType::class, [
                'label' => 'Liczba zamówień',
                'attr' => ['class' => 'form-control']
            ])
            ->add('CustomersBalance', NumberType::class, [
                'label' => 'Saldo klienta',
                'attr' => ['class' => 'form-control']
            ])
            ->add('CustomersFirma', TextType::class, [
                'label' => 'Firma klienta',
                'attr' => ['class' => 'form-control']
            ])
            ->add('PrinterId', TextType::class, [
                'label' => 'ID Drukarki',
                'attr' => ['class' => 'form-control']
            ])
            ->add('PrinterName', TextType::class, [
                'label' => 'Nazwa Drukarki',
                'attr' => ['class' => 'form-control']
            ])
            ->add('city', TextType::class, [
                'label' => 'Miasto',
                'attr' => ['class' => 'form-control']
            ])
            ->add('LastReview',TextType::class, [
                'label' => 'Ostatnia wizyta',
                'attr' => ['class' => 'form-control']
            ])
            ->add('NazwaShort', TextType::class, [
                'label' => 'Krótka nazwa',
                'attr' => ['class' => 'form-control']
            ])
            ->add('technologia', TextType::class, [
                'label' => 'Technologia',
                'attr' => ['class' => 'form-control']
            ])
            ->add('lokalny', TextType::class, [
                'label' => 'Lokalny',
                'attr' => ['class' => 'form-control']
            ])
            ->add('B2bB2c', TextType::class, [
                'label' => 'B2bB2c',
                'attr' => ['class' => 'form-control']
            ])
            ->add('ProductRodzajNazwa', TextType::class, [
                'label' => 'Rodzaj produktu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('ProductTypNazwa', TextType::class, [
                'label' => 'Nazwa typu produktu',
                'attr' => ['class' => 'form-control']
            ])
            ->add('odroczony', TextType::class, [
                'label' => 'Odroczony',
                'attr' => ['class' => 'form-control']
            ])
            ->add('publiczny', TextType::class, [
                'label' => 'Publiczny',
                'attr' => ['class' => 'form-control']
            ])
            ->add('przedszkole', TextType::class, [
                'label' => 'Przedszkole',
                'attr' => ['class' => 'form-control']
            ])
            ->add('SegmentProductType', ChoiceType::class, [
                'label' => 'Segment - Typ produktu',
                'choices' => [
                    'TEST' => 'TEST',
                    'ORYGINALNE' => 'ORYGINALNE',
                    'INNE' => 'INNE',
                    'ZAMIENNIKI' => 'ZAMIENNIKI',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('SegmentOtwierane', ChoiceType::class, [
                'label' => 'Segment - Otwierane',
                'choices' => [
                    'TEST' => 'TEST',
                    'KIEDYSOTWIERANE' => 'KIEDYSOTWIERANE',
                    'NIEOTWIERANE' => 'NIEOTWIERANE',
                    'OTWIERANE' => 'OTWIERANE',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('SegmentFirma', ChoiceType::class, [
                'label' => 'Segment - Firma',
                'choices' => [
                    'TEST' => 'TEST',
                    'B2B' => 'B2B',
                    'B2C' => 'B2C',
                    'unknown' => 'unknown',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('SegmentCzas', ChoiceType::class, [
                'label' => 'Segment - Czas',
                'choices' => [
                    'TEST' => 'TEST',
                    'CZESTO' => 'CZESTO',
                    'RZADKO' => 'RZADKO',
                    'NIGDY' => 'NIGDY',
                    'DAWNO' => 'DAWNO',
                    '' => '',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('ABTest', ChoiceType::class, [
                'label' => 'A/B Test',
                'choices' => [
                    'A' => 'A',
                    'B' => 'B',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('AddDate', DateType::class, [
                'label' => 'Data dodania',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'form-control']
            ])
            ->add('segment', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}