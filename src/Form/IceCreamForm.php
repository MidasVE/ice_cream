<?php

namespace Drupal\ice_cream\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use mysql_xdevapi\Exception;

class IceCreamForm extends FormBase
{
    public function getFormId()
    {
        // TODO: Implement getFormId() method.
        return '';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // TODO: Implement buildForm() method.

        //$form['#theme'] = 'ice_cream';

        $form['name'] = [
            '#type' => 'select',
            '#title' => t("Choose what food you'd like to eat"),
            '#options' => [
                'ice cream' => t('Ice cream'),
                'waffle' => t('Waffle'),
            ],
            '#default_value' => 'Ice cream',
        ];

        $form['flavour'] = [
            '#type' => 'select',
            '#title' => t("Choose the flavour"),
            '#options' => [
                'vanilla' => t('Vanilla'),
                'strawberry' => t('Strawberry'),
                'chocolate' => t('Chocolate')
            ],
            '#empty_option' => t('Select a flavour'),
            '#default_value' => NULL,
            '#states' => [
                'invisible' => [
                    ':input[name="name"]' => [
                        'value' => 'waffle',
                    ]
                ]
            ]
        ];

        $form['topping'] = [
            '#type' => 'select',
            '#title' => t("Choose the topping"),
            '#options' => [
                'whipped cream' => t('Whipped cream'),
                'strawberries' => t('Strawberries'),
                'chocolate chips' => t('Chocolate Chips')
            ],
            '#empty_option' => t('Select a topping'),
            '#default_value' => NULL,
            '#states' => [
                'invisible' => [
                    ':input[name="name"]' => [
                        'value' => 'ice cream',
                    ]
                ]
            ]
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Submit')
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
        $name = $form_state->getValue('name');
        $flavour = $form_state->getValue('flavour');
        $topping = $form_state->getValue('topping');
        $time = \Drupal::service('datetime.time')->getRequestTime();

        $this->createOrder($name, $flavour, $topping, $time);

        $iceCreamCount = $this->getCount('ice cream', '', '')->fetchField();
        $waffleCount = $this->getCount('waffle', '', '')->fetchField();

        $orders = $this->getOrders();

        $messenger = \Drupal::messenger();
        $messenger->addMessage('Your order was placed. There have been ' . $iceCreamCount . ' ice cream and ' . $waffleCount . ' waffle orders.');

        $maxIcecreams = \Drupal::state()->get('max_icecreams');
        $maxWaffles = \Drupal::state()->get('max_waffles');

        $iceCreamsLeft = $maxIcecreams - $iceCreamCount;
        $wafflesLeft = $maxWaffles - $waffleCount;

        if ($iceCreamCount >= $maxIcecreams || $waffleCount >= $maxWaffles) {
            $messenger->addMessage("You've ordered enough ice creams.");
            $messenger->addMessage('These are the things you ordered:');
            foreach ($orders as $order) {
                $messenger->addMessage(
                    $this->getCount('', $order->topping, $order->flavour)->fetchField() . 'x ' . $order->name . " " . $order->flavour . $order->topping
                );
            }
            db_truncate('orders')->execute();
        } else {
            $messenger->addMessage('You can still order ' . $iceCreamsLeft . ' ice creams and ' . $wafflesLeft . ' waffles.');
        }
    }

    public function createOrder($name, $flavour, $topping, $time)
    {
        try {
            \Drupal::database()->insert('orders')
                ->fields([
                    'name' => $name,
                    'flavour' => $flavour,
                    'topping' => $topping,
                    'time_clicked' => $time
                ])
                ->execute();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCount($name, $topping, $flavour)
    {
        $select = \Drupal::database()->select('orders');

        if ($topping !== '') {
            $select->condition('topping', $topping);
        } else if ($flavour !== '') {
            $select->condition('flavour', $flavour);
        } else {
            $select->condition('name', $name);
        }
        return $result = $select
            ->countQuery()
            ->execute();
    }

    public function getOrders()
    {
        try {
            $select = \Drupal::database()->select('orders', 'o');
            $result = $select
                ->fields('o', ['name', 'flavour', 'topping'])
                ->execute()
                ->fetchAll();
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}