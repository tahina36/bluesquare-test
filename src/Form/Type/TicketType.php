<?php

namespace App\Form\Type;

use App\Entity\Project;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $edit = $options['edit'];
        if ($edit) {
            $builder
                ->add('status', ChoiceType::class, [
                    'choices'  => [
                        'Ouvert' => 1,
                        'En attente du client' => 2,
                        'Tests client' => 3,
                    ],
                    'attr' => ['class' => 'form-control']
                ])
                ->add('priority', ChoiceType::class, [
                    'choices'  => [
                        'Faible' => 1,
                        'Moyenne' => 2,
                        'Haute' => 3,
                    ],
                    'attr' => ['class' => 'form-control']
                ]);

        }
        else {
            $builder
                ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
                ->add('content', TextareaType::class, ['attr' => ['class' => 'form-control']])
                ->add('status', ChoiceType::class, [
                    'choices'  => [
                        'Ouvert' => 1,
                        'En attente du client' => 2,
                        'Tests client' => 3,
                    ],
                    'attr' => ['class' => 'form-control']
                ])
                ->add('priority', ChoiceType::class, [
                    'choices'  => [
                        'Faible' => 1,
                        'Moyenne' => 2,
                        'Haute' => 3,
                    ],
                    'attr' => ['class' => 'form-control']
                ])
                ->add('type', ChoiceType::class, [
                    'choices'  => [
                        'Demande d\'amélioration' => 1,
                        'Problème technique' => 2,
                    ],
                    'attr' => ['class' => 'form-control']
                ])
                ->add('project', EntityType::class, [
                    'class' => Project::class,
                    'query_builder' => function (EntityRepository $er) use($user) {
                        return $er->createQueryBuilder('p')
                            ->join('p.permissions', 'gp')
                            ->join('gp.group', 'g')
                            ->join('g.users', 'ug')
                            ->where('ug.user = :userId')
                            ->setParameter('userId', $user)
                            ->orderBy('p.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'attr' => ['class' => 'form-control']
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'user' => User::class,
            'edit' => Boolean::class
        ]);
    }
}
