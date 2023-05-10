<?php

namespace App\BackOffice\Form;

use App\BackOffice\Repository\PlaylistRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;

class PlaylistType extends AbstractType
{

    public function __construct(PlaylistRepository $playlistRepository, EntityManagerInterface $entityManager)
    {
        $this->playlistRepository = $playlistRepository;
        $this->entityManager = $entityManager;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'mapped' => false,
            ])
            ->add('description', TextType::class, [
                'mapped' => false,
                'required' => false,
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
