<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('date_of_birth',DateType::class,
                [
                    'label'=>"Date of Birth",
                    'widget'=>"single_text",
                    'attr'=>[
                        'class'=>"datepicker"
                    ],])
            ->add('gender',ChoiceType::class,[
                'choices'=>[
                    'Male'=>'M',
                    'Female'=>'F',
                    'Other'=>'O',
                ],
            ])
            ->add('image',FileType::class,['data_class'=>null])
            ->add('note')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
