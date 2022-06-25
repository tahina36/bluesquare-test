<?php

namespace App\Form\Type;

use App\Entity\Project;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'user' => User::class
        ]);
    }
}
