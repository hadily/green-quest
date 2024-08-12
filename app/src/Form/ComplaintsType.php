<?php

namespace App\Form;

use App\Entity\Admin;
use App\Entity\Article;
use App\Entity\Complaints;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComplaintsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject')
            ->add('details')
            ->add('status')
            ->add('Owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('relatedTo', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'id',
            ])
            ->add('admin', EntityType::class, [
                'class' => Admin::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Complaints::class,
        ]);
    }
}
