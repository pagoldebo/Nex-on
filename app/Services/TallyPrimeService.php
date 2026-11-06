<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Collection;

class TallyPrimeService
{
    /**
     * Generate standard Tally Prime Sales Voucher XML for an Order.
     */
    public function generateSalesVoucherXml(Order $order): string
    {
        $date = $order->created_at->format('Ymd');
        $effectiveDate = $order->created_at->format('d-M-Y');
        $ref = htmlspecialchars($order->reference, ENT_XML1);
        $companyName = htmlspecialchars(Setting::get('store_name', config('app.name', 'NEXON CMS STORE')), ENT_XML1);

        $partyLedger = $order->payment_method === 'cash' 
            ? 'Cash' 
            : htmlspecialchars($order->shipping_name ?: 'Sundry Debtors Retail', ENT_XML1);

        // Tax calculation: Assume inclusive 18% GST (9% CGST + 9% SGST for intra-state, or 18% IGST)
        $subtotal = $order->subtotal;
        $discount = $order->discount;
        $netTaxable = max(0, round(($subtotal - $discount) / 1.18, 2));
        $taxTotal = round(($subtotal - $discount) - $netTaxable, 2);
        $isInterState = !empty($order->shipping_state) && strtolower(trim($order->shipping_state)) !== 'karnataka';
        
        $cgst = $isInterState ? 0 : round($taxTotal / 2, 2);
        $sgst = $isInterState ? 0 : round($taxTotal / 2, 2);
        $igst = $isInterState ? $taxTotal : 0;

        $xml = [];
        $xml[] = '    <TALLYMESSAGE xmlns:UDF="TallyUDF">';
        $xml[] = '      <VOUCHER VCHTYPE="Sales" ACTION="Create" OBJVIEW="Invoice Voucher View">';
        $xml[] = "        <DATE>{$date}</DATE>";
        $xml[] = "        <REFERENCEDATE>{$date}</REFERENCEDATE>";
        $xml[] = "        <VOUCHERTYPENAME>Sales</VOUCHERTYPENAME>";
        $xml[] = "        <VOUCHERNUMBER>{$ref}</VOUCHERNUMBER>";
        $xml[] = "        <REFERENCE>{$ref}</REFERENCE>";
        $xml[] = "        <PARTYLEDGERNAME>{$partyLedger}</PARTYLEDGERNAME>";
        $xml[] = "        <PARTYNAME>{$partyLedger}</PARTYNAME>";
        $xml[] = "        <PERSISTEDVIEW>Invoice Voucher View</PERSISTEDVIEW>";
        $xml[] = "        <ISINVOICE>Yes</ISINVOICE>";
        $xml[] = "        <NARRATION>Online Store Sale - Ref #{$ref} via " . strtoupper($order->payment_method) . " (Channel: {$order->channel})</NARRATION>";

        // 1. Party Ledger (Debit total)
        $xml[] = '        <ALLLEDGERENTRIES.LIST>';
        $xml[] = "          <LEDGERNAME>{$partyLedger}</LEDGERNAME>";
        $xml[] = '          <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>';
        $xml[] = "          <AMOUNT>-{$order->total}</AMOUNT>";
        $xml[] = '        </ALLLEDGERENTRIES.LIST>';

        // 2. Sales Account (Credit net taxable)
        $xml[] = '        <ALLLEDGERENTRIES.LIST>';
        $xml[] = '          <LEDGERNAME>Sales - Apparel &amp; Lingerie</LEDGERNAME>';
        $xml[] = '          <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>';
        $xml[] = "          <AMOUNT>{$netTaxable}</AMOUNT>";
        $xml[] = '        </ALLLEDGERENTRIES.LIST>';

        // 3. Taxes
        if ($cgst > 0) {
            $xml[] = '        <ALLLEDGERENTRIES.LIST>';
            $xml[] = '          <LEDGERNAME>Output CGST @ 9%</LEDGERNAME>';
            $xml[] = '          <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>';
            $xml[] = "          <AMOUNT>{$cgst}</AMOUNT>";
            $xml[] = '        </ALLLEDGERENTRIES.LIST>';
        }
        if ($sgst > 0) {
            $xml[] = '        <ALLLEDGERENTRIES.LIST>';
            $xml[] = '          <LEDGERNAME>Output SGST @ 9%</LEDGERNAME>';
            $xml[] = '          <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>';
            $xml[] = "          <AMOUNT>{$sgst}</AMOUNT>";
            $xml[] = '        </ALLLEDGERENTRIES.LIST>';
        }
        if ($igst > 0) {
            $xml[] = '        <ALLLEDGERENTRIES.LIST>';
            $xml[] = '          <LEDGERNAME>Output IGST @ 18%</LEDGERNAME>';
            $xml[] = '          <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>';
            $xml[] = "          <AMOUNT>{$igst}</AMOUNT>";
            $xml[] = '        </ALLLEDGERENTRIES.LIST>';
        }

        // 4. Shipping / COD charges if applicable
        if ($order->shipping_charge > 0) {
            $xml[] = '        <ALLLEDGERENTRIES.LIST>';
            $xml[] = '          <LEDGERNAME>Courier Freight &amp; Delivery Income</LEDGERNAME>';
            $xml[] = '          <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>';
            $xml[] = "          <AMOUNT>{$order->shipping_charge}</AMOUNT>";
            $xml[] = '        </ALLLEDGERENTRIES.LIST>';
        }

        // 5. Inventory Line Allocations
        foreach ($order->items as $item) {
            $stockItemName = htmlspecialchars($item->product_name . ' (' . ($item->size ?: 'Standard') . ')', ENT_XML1);
            $sku = htmlspecialchars($item->sku ?? 'N/A', ENT_XML1);

            $xml[] = '        <ALLINVENTORYENTRIES.LIST>';
            $xml[] = "          <STOCKITEMNAME>{$stockItemName}</STOCKITEMNAME>";
            $xml[] = '          <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>';
            $xml[] = "          <RATE>₹{$item->price}</RATE>";
            $xml[] = "          <ACTUALQTY>-{$item->qty} Pcs</ACTUALQTY>";
            $xml[] = "          <BILLEDQTY>-{$item->qty} Pcs</BILLEDQTY>";
            $xml[] = "          <AMOUNT>{$item->line_total}</AMOUNT>";
            $xml[] = '          <BATCHALLOCATIONS.LIST>';
            $xml[] = '            <GODOWNNAME>Main Showroom / Central</GODOWNNAME>';
            $xml[] = "            <BATCHNAME>Primary Batch</BATCHNAME>";
            $xml[] = "            <AMOUNT>{$item->line_total}</AMOUNT>";
            $xml[] = "            <ACTUALQTY>-{$item->qty} Pcs</ACTUALQTY>";
            $xml[] = "            <BILLEDQTY>-{$item->qty} Pcs</BILLEDQTY>";
            $xml[] = '          </BATCHALLOCATIONS.LIST>';
            $xml[] = '        </ALLINVENTORYENTRIES.LIST>';
        }

        $xml[] = '      </VOUCHER>';
        $xml[] = '    </TALLYMESSAGE>';

        return implode("\n", $xml);
    }

    /**
     * Generate standard Tally Prime Receipt Voucher XML for an Order payment.
     */
    public function generateReceiptVoucherXml(Order $order): string
    {
        $date = $order->created_at->format('Ymd');
        $ref = htmlspecialchars($order->reference, ENT_XML1);
        $rcpNo = 'RCP-' . $ref;

        // Map Payment Account based on method
        $bankOrCashLedger = match ($order->payment_method) {
            'cash'     => 'Cash-in-Hand',
            'cod'      => 'COD Settlement Clearing A/c - Courier',
            'upi'      => 'HDFC Bank Retail Current A/c (UPI)',
            'card'     => 'HDFC Bank POS Terminal Clearing A/c',
            'razorpay' => 'Razorpay Gateway Clearing A/c',
            'stripe'   => 'Stripe Merchant Settlement A/c',
            'phonepe'  => 'PhonePe Merchant Escrow A/c',
            default    => 'Bank Current Account',
        };

        $partyLedger = $order->payment_method === 'cash' 
            ? 'Cash' 
            : htmlspecialchars($order->shipping_name ?: 'Sundry Debtors Retail', ENT_XML1);

        $xml = [];
        $xml[] = '    <TALLYMESSAGE xmlns:UDF="TallyUDF">';
        $xml[] = '      <VOUCHER VCHTYPE="Receipt" ACTION="Create">';
        $xml[] = "        <DATE>{$date}</DATE>";
        $xml[] = "        <VOUCHERTYPENAME>Receipt</VOUCHERTYPENAME>";
        $xml[] = "        <VOUCHERNUMBER>{$rcpNo}</VOUCHERNUMBER>";
        $xml[] = "        <REFERENCE>{$ref}</REFERENCE>";
        $xml[] = "        <NARRATION>Payment received for Order #{$ref} via " . strtoupper($order->payment_method) . " (Txn: {$order->payment_reference})</NARRATION>";

        // Debit Bank / Cash
        $xml[] = '        <ALLLEDGERENTRIES.LIST>';
        $xml[] = "          <LEDGERNAME>{$bankOrCashLedger}</LEDGERNAME>";
        $xml[] = '          <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>';
        $xml[] = "          <AMOUNT>-{$order->total}</AMOUNT>";
        $xml[] = '        </ALLLEDGERENTRIES.LIST>';

        // Credit Party Ledger
        $xml[] = '        <ALLLEDGERENTRIES.LIST>';
        $xml[] = "          <LEDGERNAME>{$partyLedger}</LEDGERNAME>";
        $xml[] = '          <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>';
        $xml[] = "          <AMOUNT>{$order->total}</AMOUNT>";
        $xml[] = '        </ALLLEDGERENTRIES.LIST>';

        $xml[] = '      </VOUCHER>';
        $xml[] = '    </TALLYMESSAGE>';

        return implode("\n", $xml);
    }

    /**
     * Generate standard Tally Prime Stock Journal XML from inventory logs.
     */
    public function generateStockJournalXml(Collection $inventoryLogs): string
    {
        $date = now()->format('Ymd');
        $journalNo = 'STK-JRN-' . now()->format('YmdHis');

        $xml = [];
        $xml[] = '    <TALLYMESSAGE xmlns:UDF="TallyUDF">';
        $xml[] = '      <VOUCHER VCHTYPE="Stock Journal" ACTION="Create">';
        $xml[] = "        <DATE>{$date}</DATE>";
        $xml[] = "        <VOUCHERTYPENAME>Stock Journal</VOUCHERTYPENAME>";
        $xml[] = "        <VOUCHERNUMBER>{$journalNo}</VOUCHERNUMBER>";
        $xml[] = '        <NARRATION>Inventory adjustments and warehouse consumption movements</NARRATION>';

        foreach ($inventoryLogs as $log) {
            $variant = $log->variant;
            $productName = htmlspecialchars($variant?->product?->name ?? 'Item ' . $log->sku, ENT_XML1);
            $qty = abs($log->change_qty);
            $isPositive = $log->change_qty > 0;
            $rate = $variant?->cost_price ?: round(($variant?->price ?? 1000) * 0.45);
            $amount = $qty * $rate;

            $xml[] = '        <ALLINVENTORYENTRIES.LIST>';
            $xml[] = "          <STOCKITEMNAME>{$productName}</STOCKITEMNAME>";
            $xml[] = "          <ISDEEMEDPOSITIVE>" . ($isPositive ? 'Yes' : 'No') . "</ISDEEMEDPOSITIVE>";
            $xml[] = "          <RATE>₹{$rate}</RATE>";
            $xml[] = "          <ACTUALQTY>" . ($isPositive ? "+{$qty}" : "-{$qty}") . " Pcs</ACTUALQTY>";
            $xml[] = "          <BILLEDQTY>" . ($isPositive ? "+{$qty}" : "-{$qty}") . " Pcs</BILLEDQTY>";
            $xml[] = "          <AMOUNT>{$amount}</AMOUNT>";
            $xml[] = '          <BATCHALLOCATIONS.LIST>';
            $xml[] = '            <GODOWNNAME>Main Showroom / Central</GODOWNNAME>';
            $xml[] = "            <BATCHNAME>Primary Batch</BATCHNAME>";
            $xml[] = "            <AMOUNT>{$amount}</AMOUNT>";
            $xml[] = "            <ACTUALQTY>" . ($isPositive ? "+{$qty}" : "-{$qty}") . " Pcs</ACTUALQTY>";
            $xml[] = '          </BATCHALLOCATIONS.LIST>';
            $xml[] = '        </ALLINVENTORYENTRIES.LIST>';
        }

        $xml[] = '      </VOUCHER>';
        $xml[] = '    </TALLYMESSAGE>';

        return implode("\n", $xml);
    }

    /**
     * Wrap messages inside standard Tally ENVELOPE.
     */
    public function wrapEnvelope(string $messagesXml): string
    {
        return <<<XML
<ENVELOPE>
  <HEADER>
    <TALLYREQUEST>Import Data</TALLYREQUEST>
  </HEADER>
  <BODY>
    <IMPORTDATA>
      <REQUESTDESC>
        <REPORTNAME>Vouchers</REPORTNAME>
        <STATICVARIABLES>
          <SVCURRENTCOMPANY>Retail Operations</SVCURRENTCOMPANY>
        </STATICVARIABLES>
      </REQUESTDESC>
      <REQUESTDATA>
{$messagesXml}
      </REQUESTDATA>
    </IMPORTDATA>
  </BODY>
</ENVELOPE>
XML;
    }
}
