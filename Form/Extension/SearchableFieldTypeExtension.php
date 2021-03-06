<?php

namespace Ibrows\SimpleSearchBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchableFieldTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'search_field' => 'id',
            'search_condition' => '=',
            'search_method' => false,
        ]);
        $resolver->setDefined([
            'search_class',
        ]);
    }
    
    public function getExtendedType() {
        return 'form';
    }
}
