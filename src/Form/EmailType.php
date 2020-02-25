<?php

namespace App\Form;

use App\Entity\Email;
use App\Service\AliasApiInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType as ExtensionEmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmailType extends AbstractType
{

    private $api;

    private $translator;

    public function __construct(AliasApiInterface $api, TranslatorInterface $translator)
    {
        $this->api = $api;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['edition']) {
            $builder
                ->add('target', ChoiceType::class, [
                    'label' => $this->translator->trans("Target"),
                    'choices' => $this->api->getEmails(),
                    'choice_label' => function ($value) {
                        return $value;
                    },
                ])
                ->add('alias', ExtensionEmailType::class, [
                    'label' => $this->translator->trans("Alias"),
                ]);
        }

        $builder
            ->add('tags', ChoiceType::class, [
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'select2'],
            ]);

        $builder->get('tags')->resetViewTransformers();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
            'edition' => false,
        ]);
    }
}
