<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

class ModifyUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastName', TextType::class, [
            'required' => true,
            'label' => 'Last name',
            'attr' => []
        ])
        ->add('firstName', TextType::class, [
            'required' => true,
            'label' => 'First name',
            'attr' => []
        ])
        ->add('phone', TextType::class, [
            'required' => false,
            'label' => 'Phone',
            'attr' => []
        ])
        ->add('address', TextType::class, [
            'required' => true,
            'label' => 'Adress',
            'attr' => []
        ])
        ->add('city', TextType::class, [
            'required' => true,
            'label' => 'City',
            'attr' => []
        ])
        ->add('postalcode', TextType::class, [
            'required' => true,
            'label' => 'Postal Code',
            'attr' => []
        ])
        ->add('province', ChoiceType::class, [
            'choices'=>[
                'Alberta'=> 'AB',
                'British Columbia'=>'BC',
                'Manitoba' => 'MB',
                'New Brunswick' => 'NB',
                'Newfoundland and Labrador' => 'NL',
                'Northwest Territories' => 'NT',
                'Nova Scotia' => 'NS',
                'Nunavut' => 'NU',
                'Ontario' => 'ON',
                'Prince Edward Island' => 'PE',
                'Québec' => 'QC',
                'Saskatchewan' => 'SK',
                'Yukon' => 'YT',
            ],
            'required' => true,
            'label' => 'Province',
            'attr' => []
        ])
        ->add('create', SubmitType::class, [
            'label' => "Modify your information",
            'row_attr' => ['class' => 'form-button'],
            'attr' => ['class' => 'btnCreate btn-primary']
        ]);

        $builder->get('phone')->addModelTransformer(new CallbackTransformer(
            function($phoneFromDatabase) {
                $newPhone = substr_replace($phoneFromDatabase, "-", 3, 0);
                return substr_replace($newPhone, "-", 7, 0);
            }, 
            function ($phoneFromView) {
                return str_replace("-", "", $phoneFromView);
            }
        ));
        $builder->get('postalcode')->addModelTransformer(new CallbackTransformer(
            function($postalCodeFromDatabase) {
                return substr_replace($postalCodeFromDatabase, "-", 3, 0);
            }, 
            function ($postalCodeFromView) {
                return str_replace("-", "", $postalCodeFromView);
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
