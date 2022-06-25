<?php
namespace App\Form\Type;

use App\Entity\Vehicule;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Avion' => "plane",
                    'Train' => "train",
                    'Bus' => "bus"
                ],
                'attr' => ['onChange' => 'toggle_vehicule_form(this);', 'class' => 'form-control']
            ])
        ;
        $builder->add('number', TextType::class, [
            'required' => true,
            'label' => 'Saisir votre billet ou numéro de reservation',
            'attr' => ['class' => 'form-control'],
        ]);
        $builder->add('seat', TextType::class, [
            'required' => false,
            'label' => 'Saisir votre siège',
            'attr' => ['class' => 'form-control'],
        ]);
        $builder->add('bagageDrop', IntegerType::class, [
            'required' => false,
            'label' => 'Nombre de bagages',
            'attr' => ['class' => 'form-control'],
        ]);
        $builder->add('gate', TextType::class, [
            'required' => false,
            'label' => 'Saisir votre terminal',
            'attr' => ['class' => 'form-control'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Vehicule::class,
        ]);
    }
}

?>