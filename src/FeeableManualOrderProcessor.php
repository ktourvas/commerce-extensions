<?php

namespace Drupal\commerce_extensions;

use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_price\Price;

class FeeableManualOrderProcessor implements OrderProcessorInterface {

    public function process(OrderInterface $order) {

        $payment = $order->get('payment_gateway')->first()->entity;

        if( $payment->get('plugin') == 'feeable_manual_payment_gateway' ) {

            switch ( $payment->get('configuration')['fee_type'] ) {
                case 1:
                    $amount = $payment->get('configuration')['fee_value'];
                    break;
                case 2:
                    $amount = - $payment->get('configuration')['fee_value'];
                    break;
                case 3:
                    $amount = (floatval( $order->getSubtotalPrice()->getNumber() ) * intval($payment->get('configuration')['fee_value'])) / 100;
                    break;
                case 4:
                    $amount = - (floatval( $order->getSubtotalPrice()->getNumber() ) * intval($payment->get('configuration')['fee_value'])) / 100;
                    break;
            }

            $order->addAdjustment(new Adjustment([
                'type' => 'fee',
                'label' => 'Payment method fee',
                'amount' => new Price($amount, 'EUR')
            ]));

        }

    }

}