<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Category;
use Symfony\Component\Validator\Constraints\Length;

class PostFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [new Length(['min' => 3])]
            ])
            ->add('descriptiom', TextareaType::class, [
                'required' => true,
                'constraints' => [new Length(['min' => 3])]
            ])
            ->add('category', EntityType::class, ['class' => Category::class,'choice_label' => 'name'])
            ->add('save', SubmitType::class)
            // ->add('picture')
            // ->add('date')
        ;
    }
}