<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\User;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdresseType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class, [
                'attr' => [
                    'class' => 'adresse-input'
                ]
            ])
            ->add('ville', TextType::class, [
                'attr' => [
                    'class' => 'ville-input'
                ]
            ])
            ->add('cp', TextType::class, [
                'attr' => [
                    'class' => 'cp-input'
                ]
            ])
            ->add('latitude', NumberType::class, [
                'attr' => [
                    'class' => 'latitude-input'
                ]
            ])
            ->add('longitude', NumberType::class, [
                'attr' => [
                    'class' => 'longitude-input'
                ]
            ]);
            $user = $this->tokenStorage->getToken()->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $builder->add('author', EntityType::class, [
                    'label' => 'Auteur',
                    'placeholder' => 'Auteur ?',
                    'required' => false,
                    'class' => User::class,
                    'choice_label' => 'username',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                            ->orderBy('a.username', 'ASC');
                    }
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,

        ]);
    }
}
