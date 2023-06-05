<?php

namespace App\BackOffice\Form;

use App\BackOffice\Entity\Formation;
use App\BackOffice\Entity\Categorie;
use App\BackOffice\Entity\Playlist;
use App\BackOffice\Repository\PlaylistRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class FormationType extends AbstractType
{
    
    private $publishedAt;

    private $playlistRepository;

    public function __construct(PlaylistRepository $playlistRepository, EntityManagerInterface $entityManager)
    {
        $this->playlistRepository = $playlistRepository;
        $this->entityManager = $entityManager;

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('publishedatstring', DateType::class, [
                'mapped' => false,
                'constraints' => [
                    new LessThanOrEqual('today'),
                ],
            ])
            ->add('title', TextType::class, [
                'mapped' => false,
            ])
            ->add('description', TextType::class, [
                'mapped' => false,
            ])
            ->add('videoId', TextType::class, [
                'mapped' => false,
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une playlist',
                'required' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une ou plusieurs catégories',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\BackOffice\Entity\Formation::class
        ]);
    }


    public function getPublishedAtString(): string
    {
        if ($this->publishedAt == null) {
            return "";
        }
        return $this->publishedAt->format('d/m/Y');
    }

    
    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

}

