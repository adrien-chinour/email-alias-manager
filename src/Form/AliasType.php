<?php

namespace App\Form;

use App\Entity\Alias;
use App\Provider\AliasApiInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AliasType extends AbstractType
{
    private AliasApiInterface $api;

    private TranslatorInterface $translator;

    public function __construct(AliasApiInterface $api, TranslatorInterface $translator)
    {
        $this->api = $api;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('realEmail', ChoiceType::class, [
                'label' => $this->translator->trans('Target'),
                'choices' => $this->api->getEmails(),
                'attr' => ['class' => 'form-select'],
                'choice_label' => fn ($value) => $value,
            ])
            ->add('aliasEmail', TextType::class, [
                'label' => $this->translator->trans('Alias'),
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Alias $alias */
                $alias = $event->getData();

                // update alias because domain not include in form
                $alias->setAliasEmail($alias->getAliasEmail() . $alias->getDomain());
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Alias::class,
            'attr' => ['id' => 'email-form'],
        ]);
    }
}
