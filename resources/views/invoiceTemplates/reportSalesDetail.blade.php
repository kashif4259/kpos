<div class="invoice-page-wrapper">
    <div class="thermal-invoice" style="width: 95mm;">
        <!--Invoice Header-->
        <div class="text-center invoice-header mb-3">

            <!--Invoice Logo-->
            <img src="{logo_source}" width="100" class="img-fluid" alt="">
            <div class="app-name my-2">{app_name}</div>
            <div>
                <h6 class="text-uppercase font-weight-bold">Sales Invoice</h6>
            </div>
            <small class="font-weight-bold">
                {date} - {time}
            </small>

            <div style="font-size: 9px; font-style:italic; text-align:center; margin:5px auto 5px">
            Unit A-charlton house, springfield road hayes ub4 olg<br>
            07471487471<br>
            Vat: 368247271<br>
            </div>
        </div>

        <!--Invoice Info-->
        <div class="mb-3 font-weight-bold invoice-info">
            <p class="mb-1">
                <span class="font-weight-bold">Invoice ID: </span> {invoice_id}
            </p>
            <p class="mb-1">
                <span class="font-weight-bold">Sold To: </span> {customer_name}
            </p>
            <p class="mb-1">
                <span class="font-weight-bold">Sold By: </span> {employee_name}
            </p>
            <p class="mb-1">
                <span class="font-weight-bold">Phone: </span> {phone_number}
            </p>
            <p class="mb-1">
                <span class="font-weight-bold">Address: </span> {address}
            </p>
            <!-- <p class="mb-1">
                <span class="font-weight-bold">TIN: </span> {tin}
            </p> -->
           
        </div>

        <!--Invoice Body-->
        <table class="table table-borderless">
            <tbody class="font-weight-bold">
                <tr class="border-top border-bottom t-header">
                    <th class="pl-0">Items</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right pr-0">Total</th>
                </tr>
                <tr><td>{item_details}</td></tr>
                <tr class="t-footer border-top">
                    <td class="text-left">Subtotal</td>
                    <td class="font-weight-bold" colspan="4">{sub_total}</td>
                </tr>
                <tr class="t-footer">
                    <td class="text-left">Shipment</td>
                    <td class="font-weight-bold" colspan="4">{shipment_amount}</td>
                </tr>
                <tr class="t-footer">
                    <td class="text-left">Tax</td>
                    <td class="font-weight-bold" colspan="4">{tax}</td>
                </tr>
                <tr class="t-footer">
                    <td class="text-left">Discount</td>
                    <td class="font-weight-bold" colspan="4">{discount}</td>
                </tr>
                <tr class="t-footer">
                    <td class="text-left">Total</td>
                    <td class="font-weight-bold" colspan="4">{total}</td>
                </tr>
                <tr class="t-footer">
                    <td class="text-left">Payment Method</td>
                    <td class="font-weight-bold" colspan="4">{payment_details}</td>
                </tr>
                <tr class="t-footer">
                    <td class="text-left">Exchange</td>
                    <td class="font-weight-bold" colspan="4">{exchange}</td>
                </tr>
                <tr>
                    <td class="text-left">Notes</td>
                    <td>{note}</td>
                </tr>
            </tbody>
        </table>
        <div class="shipment-address">{shipment_address}</div>
        <div class="text-center">{barcode}</div>
    </div>
</div>