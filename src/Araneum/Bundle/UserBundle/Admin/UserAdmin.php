<?php
namespace Araneum\Bundle\UserBundle\Admin;

use Araneum\Bundle\UserBundle\Form\DataTransformer\UserRolesTransformer;
use Araneum\Bundle\UserBundle\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserAdmin extends Admin
{
    protected $formOptions = ['validation_groups' => ['Profile']];

    /**
     * Create/Update form
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $roleRepository = $this->getRoleRepository();

        $formMapper
            ->getFormBuilder()
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function(FormEvent $event) use ($formMapper, $subject, $roleRepository)
                {
                    $roles = new ArrayCollection();

                    foreach($subject->getRoles() as $roleName)
                    {
                        $roles->add($roleRepository->findOneByName($roleName));
                    }

                    $event
                        ->getForm()
                        ->get('roles')
                        ->setData($roles);
                }
            );

        $formMapper
            ->add('email', null, ['label' => 'email'])
            ->add('username', null, ['label' => 'username'])
            ->add('fullName', null, ['label' => 'full_name'])
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'enabled',
                    'required' => false,
                ]
            )
            ->add('roles', null, ['label' => 'roles']);

        if ($this->getSubject()->getId() === null) {
            $formMapper->add('plainPassword', 'text', ['label' => 'password']);
        }

        $formMapper
            ->get('roles')
            ->addModelTransformer(new UserRolesTransformer());
    }

    /**
     * Filters for list
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fullName', null, ['label' => 'full_name'])
            ->add('email', null, ['label' => 'email'])
            ->add('enabled', null, ['label' => 'enabled'])
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'created_at',
                ],
                null,
                ['format' => 'MM/dd/y']
            );
    }

    /**
     * Show list
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add('email', null, ['label' => 'email'])
            ->add('fullName', null, ['label' => 'full_name'])
            ->add(
                'enabled',
                null,
                [
                    'editable' => true,
                    'label' => 'enabled'
                ]
            )
            ->add('lastLogin', null, ['label' => 'last_login'])
            ->add('roles', null, ['label' => 'roles'])
            ->add(
                'createdAt',
                null,
                [
                    'format' => 'm/d/Y',
                    'label' => 'created_at'
                ]
            )
            ->add(
                'updatedAt',
                null,
                [
                    'format' => 'm/d/Y',
                    'label' => 'updated_at'
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                        'activateUser' => [
                            'template' => 'AraneumUserBundle:AdminAction:activateUser.html.twig'
                        ],
                        'recoveryPassword' => [
                            'template' => 'AraneumUserBundle:AdminAction:recoveryPassword.html.twig'
                        ],
                    ],
                    'label' => 'actions'
                ]
            );
    }

    /**
     * Get list of field for export
     *
     * @return array
     */
    public function getExportFields()
    {
        return [
            'id',
            'fullName',
            'username',
            'email',
            'enabled',
            'locked',
            'lastLogin',
            'roles',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * Get Service Container
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return RoleRepository
     */
    public function getRoleRepository()
    {
        return $this
            ->getContainer()
            ->get('doctrine')
            ->getRepository('AraneumUserBundle:Role');
    }

    /**
     * Set Service Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}