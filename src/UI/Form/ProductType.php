<?php

namespace App\UI\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre del producto',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: Camiseta de algodón'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'El nombre no puede estar vacío'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'El nombre debe tener al menos {{ limit }} caracteres',
                        'maxMessage' => 'El nombre no puede tener más de {{ limit }} caracteres'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Describe el producto...'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La descripción no puede estar vacía'
                    ])
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Precio',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: 29.99'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'El precio no puede estar vacío'
                    ]),
                    new Assert\GreaterThan([
                        'value' => 0,
                        'message' => 'El precio debe ser mayor que cero'
                    ])
                ]
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock inicial',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: 100'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'El stock no puede estar vacío'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'El stock no puede ser negativo'
                    ])
                ]
            ])
            ->add('guardar', SubmitType::class, [
                'label' => 'Crear Producto',
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
