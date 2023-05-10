<?php

namespace App\BackOffice\Form;

use App\BackOffice\Repository\CategorieRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;

class CategorieType extends AbstractType
{

    public function __construct(CategorieRepository $categorieRepository, EntityManagerInterface $entityManager)
    {
        $this->categorieRepository = $categorieRepository;
        $this->entityManager = $entityManager;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'mapped' => false,
            ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null ,
        ]);
    }
}
