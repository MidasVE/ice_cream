<?php

namespace Drupal\ice_cream\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an ice cream form block
 * @Block(
 *      admin_label = @Translation("Ice cream block"),
 *      id = "ice_cream_block"
 *     )
 */
class IceCreamBlock extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $build = \Drupal::formBuilder()->getForm('Drupal\ice_cream\Form\IceCreamForm');

//        $build = [
//            '#theme' => 'ice_cream',
//            '#form' => $form,
//        ];
        return $build;
    }

    public function getOrderCount() {
        $select = \Drupal::database()->select('orders');
        $result = $select
            ->countQuery()
            ->execute();
        $record = $result->fetchField();
        return $record;
    }
}