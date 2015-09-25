<?php

/**
* Map Connec PurchaseOrder representation to/from vTiger PurchaseOrder
*/
class PurchaseOrderMapper extends TransactionMapper {
  private $po_status_mapping = null;
  private $po_status_mapping_reverse = null;

  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'PurchaseOrder';
    $this->local_entity_name = 'PurchaseOrder';
    $this->connec_resource_name = 'purchase_orders';
    $this->connec_resource_endpoint = 'purchase_orders';

    $this->po_status_mapping = array('DRAFT' => 'Created', 'AUTHORISED' => 'Approved', 'DELIVERED' => 'Delivered', 'CANCELLED' => 'Cancelled', 'RECEIVED' => 'Received Shipment');
    $this->po_status_mapping_reverse = array('Created' => 'DRAFT', 'Approved' => 'AUTHORISED', 'Delivered' => 'DELIVERED', 'Cancelled' => 'CANCELLED', 'Received Shipment' => 'RECEIVED');
  }

  // Map the Connec resource attributes onto the vTiger PurchaseOrder
  protected function mapConnecResourceToModel($purchase_order_hash, $purchase_order) {
    parent::mapConnecResourceToModel($purchase_order_hash, $purchase_order);

    if($this->is_set($purchase_order_hash['transaction_number'])) { $purchase_order->column_fields['requisition_no'] = $purchase_order_hash['transaction_number']; }
    if($this->is_set($purchase_order_hash['public_note'])) { $purchase_order->column_fields['description'] = $purchase_order_hash['public_note']; }
    if($this->is_set($purchase_order_hash['deposit'])) { $purchase_order->column_fields['paid'] = $purchase_order_hash['deposit']; }
    if($this->is_set($purchase_order_hash['balance'])) { $purchase_order->column_fields['balance'] = $purchase_order_hash['balance']; }
    if($this->is_set($invoice_hash['due_date'])) { $invoice->column_fields['duedate'] = $this->format_date_to_php($invoice_hash['due_date']); }

    // Map status
    $purchase_order->column_fields['postatus'] = $this->po_status_mapping[$purchase_order_hash['status']];
  }

  // Map the vTiger PurchaseOrder to a Connec resource hash
  protected function mapModelToConnecResource($purchase_order) {
    $purchase_order_hash = parent::mapModelToConnecResource($purchase_order);

    // Map attributes
    $purchase_order_hash['transaction_number'] = $purchase_order->column_fields['requisition_no'];
    $purchase_order_hash['public_note'] = $purchase_order->column_fields['description'];
    $purchase_order_hash['deposit'] = $purchase_order->column_fields['paid'];
    $purchase_order_hash['balance'] = $purchase_order->column_fields['balance'];
    $invoice_hash['due_date'] = $this->format_date_to_connec($invoice->column_fields['duedate']);

    // Map status
    $purchase_order_hash['status'] = $this->po_status_mapping_reverse[$purchase_order->column_fields['postatus']];

    return $purchase_order_hash;
  }
}