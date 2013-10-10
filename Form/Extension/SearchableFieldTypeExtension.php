<?php

namespace Ibrows\SimpleSearchBundle\Form\Extension;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractTypeExtension;

class SearchableFieldTypeExtension extends AbstractTypeExtension
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'search_field' => 'id',
                'search_condition' => '=',
                'search_method' => false
        ));
        $resolver->setOptional(array(
                'search_class',
        ));
    }
    
    public function getExtendedType() {
        return 'form';
    }

}
