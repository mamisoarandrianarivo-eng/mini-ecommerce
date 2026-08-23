<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setSearchFields(['reference'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->disable(Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('status');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('reference', 'Référence')->setFormTypeOption('disabled', true);
        yield AssociationField::new('user', 'Client')->setFormTypeOption('disabled', true);
        yield TextField::new('totalAmount', 'Total (€)')->setFormTypeOption('disabled', true);
        yield ChoiceField::new('status', 'Statut')->setChoices([
            'En attente' => Order::STATUS_PENDING,
            'Payée' => Order::STATUS_PAID,
            'Expédiée' => Order::STATUS_SHIPPED,
            'Livrée' => Order::STATUS_DELIVERED,
            'Annulée' => Order::STATUS_CANCELLED,
        ])->renderAsBadges([
            Order::STATUS_PENDING => 'warning',
            Order::STATUS_PAID => 'info',
            Order::STATUS_SHIPPED => 'primary',
            Order::STATUS_DELIVERED => 'success',
            Order::STATUS_CANCELLED => 'danger',
        ]);
        yield TextField::new('shippingAddress', 'Adresse')->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Date')->setFormTypeOption('disabled', true);
    }
}
