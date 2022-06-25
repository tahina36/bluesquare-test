<?php
namespace App\Form\Type;

use App\Entity\TripStep;
use App\Form\DataTransformer\ArrayToVehiculeTranformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripStepType extends AbstractType
{
    private $transformer;

    public function __construct(ArrayToVehiculeTranformer $arrayToVehiculeTranformer) {
        $this->transformer = $arrayToVehiculeTranformer;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TripStep::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehicule', VehiculeType::class)
            ->add('departure', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label'=> 'Ville de départ'
            ])
            ->add('arrival', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Ville d\'arrivée'
            ])
            ->add('departureDate', DateType::class, [
                'widget' => 'choice',
                'label'=> 'Date du départ'
            ])
            ->add('arrivalDate', DateType::class, [
                'widget' => 'choice',
                'label' => 'Date de l\'arrivée'
            ])
        ;
        $builder->get('vehicule')->addModelTransformer($this->transformer);
    }
}

?>