<?php

namespace Lof\Cfcheckout\Controller\Standard;

class Response extends \Lof\Cfcheckout\Controller\CfAbstract {

    public function execute() {
        $returnUrl = $this->getCheckoutHelper()->getUrl('checkout');

        try {
            $paymentMethod = $this->getPaymentMethod();
            $params = $this->getRequest()->getParams();
            $status = $paymentMethod->validateResponse($params);
            $order = $this->getOrder();
            $orderStatus = $order->getStatus();
            
            if($orderStatus=="pending"){
                if ($status == "SUCCESS") {
                    $returnUrl = $this->getCheckoutHelper()->getUrl('checkout/onepage/success');
                    $payment = $order->getPayment();
                    $paymentMethod->postProcessing($order, $payment, $params);
                    $this->messageManager->addSuccess(__('Your payment was successful'));

                } else if ($status == "CANCELLED") {
                    $this->_eventManager->dispatch(
                        'lof_cfcheckout_controller_standard_response',
                        [
                            'order_ids' => [$order->getLastOrderId()],
                            'order' => $order,
                            'status' => $status
                        ]
                    );
                    $order->cancel()->save();
                    //$this->messageManager->addErrorMessage(__('Your payment has been cancelled'));
                    $this->_cancelPayment();
                    
                } else if ($status == "FAILED") {
                    $this->_eventManager->dispatch(
                        'lof_cfcheckout_controller_standard_response',
                        [
                            'order_ids' => [$order->getLastOrderId()],
                            'order' => $order,
                            'status' => $status
                        ]
                    );
                    $order->cancel()->save();
                    $this->_checkoutSession->restoreQuote();
                    $this->messageManager->addErrorMessage(__('Payment failed. Please try again or choose a different payment method'));
                    $returnUrl = $this->getCheckoutHelper()->getUrl('checkout/onepage/failure');
                } else if($status == "PENDING"){
                    $this->_eventManager->dispatch(
                        'lof_cfcheckout_controller_standard_response',
                        [
                            'order_ids' => [$order->getLastOrderId()],
                            'order' => $order,
                            'status' => $status
                        ]
                    );
                    $this->messageManager->addWarning(__('Your payment is pending'));

                } else{
                    $this->_eventManager->dispatch(
                        'lof_cfcheckout_controller_standard_response',
                        [
                            'order_ids' => [$order->getLastOrderId()],
                            'order' => $order,
                            'status' => $status
                        ]
                    );
                    $this->messageManager->addErrorMessage(__('There is an error.Payment status is pending'));
                    $returnUrl = $this->getCheckoutHelper()->getUrl('checkout/onepage/failure');
                }
            } else {
                $this->_eventManager->dispatch(
                    'lof_cfcheckout_controller_standard_response',
                    [
                        'order_ids' => [$order->getLastOrderId()],
                        'order' => $order,
                        'status' => $status
                    ]
                );
                $this->messageManager->addNotice(__('Your payment was already processed'));
            }    
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t place the order.'));
        }

        $this->getResponse()->setRedirect($returnUrl);
    }

}
