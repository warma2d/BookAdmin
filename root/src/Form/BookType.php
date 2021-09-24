<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(Book::NAME)
            ->add(Book::PUBLISH_YEAR, IntegerType::class)
            ->add(Book::ISBN)
            ->add(Book::NUMBER_PAGES, IntegerType::class)
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'expanded' => false,
                'multiple' => true,
                'query_builder' => function (AuthorRepository $authorRepository) {
                    return $authorRepository
                        ->createQueryBuilder('author')
                        ->andWhere('author.'.Author::DELETED_AT.' IS NULL');
                }
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary save',
                ],
                'label' => 'Сохранить',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
