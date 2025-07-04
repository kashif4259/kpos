<?php


namespace App\Http\Traits;


use App\Libraries\AllSettingFormat;
use App\Models\InvoiceTemplate;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Payments;
use App\Models\ShippingInformation;
use Illuminate\Support\Facades\Lang;
use Milon\Barcode\DNS1D;

trait InvoiceTemplateTrait
{
    public function replaceItemDetails($orderId, $template, $invoiceTemplateSize)
    {
        $itemDetails = OrderItems::itemsForInvoice($orderId);
        $allSettingFormat = new AllSettingFormat;
        $row = "";
        foreach ($itemDetails as $index => $item) {

            if ($item['variant_title'] == 'default_variant') {
                $item['variant_title'] = '';
            } elseif ($item['type'] == 'shipment') {
                $item['variant_title'] = Lang::get('lang.shipping');
                $item['quantity'] = '';
                $item['discount'] = '';
                $item['price'] = '';

            } else {
                $item['variant_title'] = " ( " . $item['variant_title'] . " ) ";
            }

            if($invoiceTemplateSize === 'large'){
                $tempTdTag = '<td>';
            }elseif ($invoiceTemplateSize === 'thermal'){
                $tempTdTag = '<td>';
            }else{
                $tempTdTag = '<td style="border-bottom: 0" colspan="4">';
            }


            if ($item['type'] == 'shipment' || $item['type'] == 'discount') {
                $newRow = '';
            } else {

                $title = $item['note'] ?
                    $tempTdTag
                    . $this->getPaddedValue($index) . ". " . $item['title'] . $item['variant_title'] . '<br><small>Note: '
                    . $item['note'] . '</small></td>'
                    : $tempTdTag
                    . $this->getPaddedValue($index) . ". " . $item['title'] . $item['variant_title'] . '</td>';


                if ($invoiceTemplateSize === 'small') {
                    $newRow = $this->prepareTableForSmall($title, $item, $allSettingFormat);
                } elseif ($invoiceTemplateSize === 'thermal') {
                    $newRow = $this->prepareTableForThermal($title, $item, $allSettingFormat);
                } else {
                    $newRow = $this->prepareTableForLarge($title, $item, $allSettingFormat);
                }
            }


            $row = $row . $newRow;
        }

        $searchFor = '<tr><td>{item_details}</td></tr>';
        return str_replace($searchFor, $row, $template);
    }

    public function prepareTableForThermal($title, $item, $allSettingFormat)
    {
        $tempTitleDiv = '<tr>' . $title . '</tr><tr>';

        return $tempTitleDiv . '<td>' .
            $allSettingFormat->getCurrency($allSettingFormat->thousandSep($item['price'])) .' x '.intval($allSettingFormat->thousandSep($item['quantity'])). '</td>
                <td class="dis">' . $allSettingFormat->thousandSep($item['discount']) . '%</td>
                <td>' . $allSettingFormat->getCurrency($allSettingFormat->thousandSep($item['sub_total'])) . '</td>
            </tr>';
    }

    public function prepareTableForLarge($title, $item, $allSettingFormat)
    {
        $tempTitleDiv = '<tr>' . $title;

        return $tempTitleDiv . '<td class="text-right">' . intval($allSettingFormat->thousandSep($item['quantity'])) . '</td>
                <td class="text-right ">' . $allSettingFormat->getCurrency($allSettingFormat->thousandSep($item['price'])) . '</td>
                <td class="text-right">' . $allSettingFormat->thousandSep($item['discount']) . '%</td>
                <td class="text-right pr-0">' . $allSettingFormat->getCurrency($allSettingFormat->thousandSep($item['sub_total'])) . '</td>
            </tr>';
    }

    public function prepareTableForSmall($title, $item, $allSettingFormat)
    {
        $tempTitleDiv = '<tr>' . $title . '</tr><tr>';

        return $tempTitleDiv . '<td class="w-25">' . $allSettingFormat->getCurrency($allSettingFormat->thousandSep($item['price'])) . '</td>
                <td class="text-right pr-0">' . intval($allSettingFormat->thousandSep($item['quantity'])) . '</td>
                <td class="text-right pr-0">' . $allSettingFormat->thousandSep($item['discount']) . '%</td>
                <td class="text-right pr-0">' . $allSettingFormat->getCurrency($allSettingFormat->thousandSep($item['sub_total'])) . '</td>
            </tr>';

    }

    public function replacePaymentDetails($orderId, $template, $invoiceTemplateSize)
    {
        $getCustomPaymentType =  Order::where('id',$orderId)->first();
        $allSettingFormat = new AllSettingFormat;
        $customPaymentType = $getCustomPaymentType->custom_payment_type;
        $paymenttype = '';
        if($customPaymentType == 'cash'){
            $paymenttype = 'Cash';
        }elseif($customPaymentType == 'credit-card'){
            $paymenttype = 'Credit Card';
        }elseif($customPaymentType == 'debit-card'){
            $paymenttype = 'Debit Card';
        }elseif($customPaymentType == 'split-payment'){
            $paymenttype = 'Split Payment';
        }
        $row = $paymenttype;

        $searchFor = '{payment_details}';

        return str_replace($searchFor, $row, $template);

    }

    public function getTemplate($cashRegisterId, $orderType)
    {
        if ($cashRegisterId == null) {
            $data = InvoiceTemplate::getInvoiceTemplateForNoCashReg($orderType);
        } else {
            $data = InvoiceTemplate::getInvoiceTemplateToPrint($cashRegisterId, $orderType);
        }

        return $data;
    }

    public function singleReportInvoiceTemplate($cashRegisterId, $orderType, $invoiceSize = 'large')
    {
        if ($cashRegisterId == null) {
            $data = InvoiceTemplate::getReportTemplateWithoutCashRegister($orderType, $invoiceSize);
        } else {

            $data = InvoiceTemplate::getReportTemplateWithCashRegister($cashRegisterId, $orderType);
        }

        return $data;
    }

    public function getInvoiceLogo($from, $template, $orderType)
    {
        $configKey = $orderType === 'sales' ? 'invoiceLogo' : 'purchase_invoiceLogo';
        $src = url('/') . '/uploads/logo/' . config($configKey);
        $template = str_replace('{logo_source}', $src, $template);

        return $template;
    }
    public function getInvoiceLogoNew($from, $template, $orderType)
    {
        $src = url('/images/new_logo.png');
        $template = str_replace('{logo_source}', $src, $template);
        return $template;
    }

    public function replaceSpecificInfo($template, $orderId, $orderType, $cashRegisterId, $salesOrReceivingType, $transferBranchName)
    {
        $allSettingFormat = new AllSettingFormat;
        $orderDetails = Order::getOrderDetailsForInvoice($orderId, $orderType, $cashRegisterId);
        $discountAmount = OrderItems::getDiscountAmount($orderId);

        //customer
        if ($orderDetails->customer_name == null) {
            $orderDetails->customer_name = Lang::get('lang.walk_in_customer');
        }

        $replace = $this->replaceInvoiceRandomInfo($orderDetails, $allSettingFormat);
        $replace['{discount}'] = $this->replaceDiscount($discountAmount, $allSettingFormat);
        $template = $this->replaceShipmentDetails($template, $orderId, $orderType);

        $replace['{table_name}'] = '';
        if ($orderDetails->table_id != null) {
            $replace['{table_name}'] = $orderDetails->table_name;
        }

        $orderDetails->tin_number ? $replace['{tin}'] = $orderDetails->tin_number : $replace['{tin}'] = '';

        if ($orderDetails->invoice_id) {
            $barcode = '<img src="data:image/png;base64,' . (new DNS1D)->getBarcodePNG($orderDetails->invoice_id, "C39") . '" alt="barcode" />';
            $template = str_replace('{barcode}', $barcode, $template);
        }


        if ($orderType == 'sales') {

            $orderDetails->sales_note ? $replace['{note}'] = $orderDetails->sales_note : $replace['{note}'] = '';
            $orderDetails->phone_number ? $replace['{phone_number}'] = $orderDetails->phone_number : $replace['{phone_number}'] = '';
            $orderDetails->address ? $replace['{address}'] = $orderDetails->address : $replace['{address}'] = '';
            $salesOrReceivingType == 'customer' ? $replace['{customer_name}'] = $orderDetails->customer_name : $replace['{customer_name}'] = $transferBranchName;

        } else {
            if ($salesOrReceivingType == 'supplier') {
                if ($orderDetails->supplier_name == null) {
                    $orderDetails->supplier_name = Lang::get('lang.walk_in_supplier');
                }
                $replace['{supplier_name}'] = $orderDetails->supplier_name;
            } else {
                $replace['{supplier_name}'] = $transferBranchName;
            }
        }

        return strtr($template, $replace);
    }

    public function replaceShipmentDetails($template, $orderId, $orderType)
    {
        $allSettingFormat = new AllSettingFormat;
        $shipmentInformation = ShippingInformation::orderShipment($orderId);

        $shippingAddress = '';
        if ($shipmentInformation != null) {
            $shippingAddress = '<p class="mb-1">
                        <span class="font-weight-bold">Shipping Address: </span>' . $shipmentInformation->shipping_address . '
                    </p>';
        }

        if ($orderType == 'sales') {

            $price = $shipmentInformation ? $allSettingFormat->getCurrency($allSettingFormat->thousandSep($shipmentInformation->price)) : 'N/A';
            $template = str_replace('{shipment_amount}', $price, $template);
            $template = str_replace('{shipment_address}', $shippingAddress, $template);
        }

        return $template;
    }

    public function replaceInvoiceRandomInfo($orderDetails, $allSettingFormat)
    {
        return [
            '{app_name}' => config('app_name'),
            '{invoice_id}' => $orderDetails->invoice_id,
            '{employee_name}' => $orderDetails->employee_name,
            '{date}' => $allSettingFormat->getDate($orderDetails->date),
            '{time}' => $allSettingFormat->timeFormat($orderDetails->created_at),
            '{sub_total}' => $allSettingFormat->getCurrency($allSettingFormat->thousandSep($orderDetails->sub_total)),
            '{tax}' => $allSettingFormat->getCurrency($allSettingFormat->thousandSep($orderDetails->total_tax)),
            '{total}' => $allSettingFormat->getCurrency($allSettingFormat->thousandSep($orderDetails->total)),
            '{exchange}' => $allSettingFormat->getCurrency($allSettingFormat->thousandSep($orderDetails->exchange)),
        ];
    }

    public function replaceDiscount($discountAmount, $allSettingFormat)
    {
        if ($discountAmount != null) {
            return $allSettingFormat->getCurrency($allSettingFormat->thousandSep($discountAmount->overAllDiscount));
        } else {
            return $allSettingFormat->getCurrency($allSettingFormat->thousandSep(0.00));
        }
    }

    public function getPaddedValue($value)
    {
        return str_pad($value + 1, 2, "0", STR_PAD_LEFT);
    }

    public function replaceThermalPaymentDetails($orderId, $template, $invoiceTemplateSize)
    {
        $allSettingFormat = new AllSettingFormat;
        $paymentDetails = Payments::getPaymentDetails($orderId);

        $getCustomPaymentType =  Order::where('id',$orderId)->first();

        $customPaymentType = $getCustomPaymentType->custom_payment_type;
        $paymenttype = '';
        if($customPaymentType == 'cash'){
            $paymenttype = 'Cash';
        }elseif($customPaymentType == 'credit-card'){
            $paymenttype = 'Credit Card';
        }elseif($customPaymentType == 'debit-card'){
            $paymenttype = 'Debit Card';
        }elseif($customPaymentType == 'split-payment'){
            $paymenttype = 'Split Payment';
        }

        $row = "";

        // foreach ($paymentDetails as $item) {

        //     $newRow = '
        //     <tr>
        //         <td>' . $item['name'] . '</td>
        //         <td>' . $allSettingFormat->getCurrency($allSettingFormat->thousandSep($item['paid'])) . '</td>
        //     </tr>';
        //     $row = $row . $newRow;
        // }

        $row = '<tr>
        <td>Payment Mehtod</td>
        <td>' . $paymenttype . '</td>
    </tr>';

        $searchFor = '<tr>{payment_details}</tr>';

        return str_replace($searchFor, $row, $template);

    }
}