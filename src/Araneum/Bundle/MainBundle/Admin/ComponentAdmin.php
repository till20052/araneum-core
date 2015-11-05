<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\ApplicationEvents;
use Araneum\Bundle\MainBundle\Entity\Component;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Araneum\Bundle\MainBundle\Form\DataTransformer\ComponentOptionsTransformer;
use Araneum\Bundle\MainBundle\Form\Type\ComponentOptionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ComponentAdmin
 * @package Araneum\Bundle\MainBundle\Admin
 */
class ComponentAdmin extends Admin
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

	/**
	 * Invoke method after creation of component
	 *
	 * @param Component $component
	 * @return void
	 */
	public function postPersist($component)
	{
		$event = new ApplicationEvent();

		$event->setApplications($component->getApplications());

		$this->dispatcher->dispatch(ApplicationEvents::POST_PERSIST, $event);
	}

	/**
	 * Invoke method after modification of component
	 *
	 * @param Component $component
	 * @return void
	 */
	public function postUpdate($component)
	{
		$event = new ApplicationEvent();

		$event->setApplications($component->getApplications());

		$this->dispatcher->dispatch(ApplicationEvents::POST_UPDATE, $event);
	}

	/**
	 * Invoke method after deletion of component
	 *
	 * @param Component $component
	 * @return void
	 */
	public function postRemove($component)
	{
		$event = new ApplicationEvent();

		$event->setApplications($component->getApplications());

        $this->dispatcher->dispatch(ApplicationEvents::PRE_REMOVE, $event);
	}

	/**
	 * Set Event Dispatcher
	 *
	 * @param EventDispatcherInterface $eventDispatcherInterface
	 */
	public function setDispatcher(EventDispatcherInterface $eventDispatcherInterface)
	{
		$this->dispatcher = $eventDispatcherInterface;
	}

    /**
     * Create/Update Component Form
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper
            ->getFormBuilder()
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function(FormEvent $event) use ($formMapper, $subject)
                {
                    $options = new ArrayCollection();

                    foreach($subject->getOptions() as $key => $value)
                    {
                        $options[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }

                    $event
                        ->getForm()
                        ->get('options')
                        ->setData($options);
                }
            );

        $formMapper
            ->add('name', 'text', ['label' => 'name'])
            ->add(
                'applications',
                'sonata_type_model',
                [
                    'multiple' => true,
                    'by_reference' => false,
                    'required' => false
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'label' => 'description',
                    'required' => false
                ]
            )
            ->add(
                'options',
                'collection',
                [
                    'label' => 'options',
                    'type' => new ComponentOptionType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'options' => [
                        'label' => false
                    ]
                ]
            )
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'enabled',
                    'required' => false
                ]
            )
            ->add(
                'default',
                'checkbox',
                [
                    'label' => 'default',
                    'required' => false
                ]
            );

        $formMapper
            ->get('options')
            ->addModelTransformer(new ComponentOptionsTransformer());
    }

    /**
     * Component List
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add(
                'name',
                null,
                [
                    'label' => 'name',
                    'editable' => true
                ]
            )
            ->add('applications', null, ['labels' => 'applications'])
            ->add('description', null, ['label' => 'description'])
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                    'editable' => true
                ]
            )
            ->add(
                'default',
                null,
                [
                    'label' => 'default',
                    'editable' => true
                ]
            )
            ->add(
                'createdAt',
                'datetime',
                [
                    'label' => 'created_at',
                    'format' => 'm/d/Y'
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'label' => 'actions',
                    'actions' => [
                        'edit' => [],
                        'delete' => []
                    ]
                ]
            );
    }

    /**
     * Component Filters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'name'])
            ->add('applications', null, ['label' => 'applications'])
            ->add('description', null, ['label' => 'description'])
            ->add('enabled', null, ['label' => 'enabled'])
            ->add('default', null, ['label' => 'default'])
            ->add(
                'createdAt',
                'doctrine_orm_datetime_range',
                [
                    'label' => 'created_at',
                    'field_type' => 'sonata_type_datetime_range_picker'
                ],
                null,
                [
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy'
                ]
            );
    }

    /**
     * Component form validator
     *
     * @param ErrorElement $errorElement
     * @param Component $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        foreach($object->getOptions() as $key => $value)
        {
            if(
                preg_match('/^([A-z])([\w\d\/\_]+)$/', $key)
                && preg_match('/^([\w\d\/\_]+)$/', $value)
            ){
                continue;
            }

            $errorElement->addViolation('One or more tokens of options have not valid key or value');
        }
    }
}