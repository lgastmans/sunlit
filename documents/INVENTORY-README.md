Inventory
=========

table 'Inventory' is a one-to-one relationship with products, holds the 3 different stock values
table 'Inventory Movements' tracks the movement of stock from both the Purchase Orders and Sales Orders


Inventory table
---------------
id
warehouse_id
product_id
purchase_order_id 	references Purchase Order, null if Sales Order
sales_order_id		references Sales Order, null if Purchase Order
quantity
date_created
user_id
soft-deletes


Inventory Movement table
------------------------
id
warehouse_id
product_id
purchase_order_id
quantity
user_id
movement_type 		1 - RECEIVED | 2 - DELIVERED | (RETURNED | CANCELLED | CORRECTED...)


Purchase Order (PO)
-------------------
1 Draft			
2 Ordered			
3 Confirmed		- trigger stock ordered, update Ordered Stock (add)
4 Shipped			
5 Customs			
6 Cleared			
7 Received		- trigger stock in, update Available Stock (add), update Ordered Stock (deduct), create a RECEIVED Inventory Movement 


Sales Order (SO)
----------------
    const DRAFT = 1;
    const ORDERED = 2;
    const CONFIRMED = 3;
    const SHIPPED = 4;
    // const CUSTOMS = 5;
    // const CLEARED = 6;
    const DELIVERED = 7;

Draft
Ordered
Confirmed		- trigger stock out, update Booked Stock (add)
Shipped			
Delivered		- trigger stock out, update Available Stock (deduct), update Booked Stock (deduct), create a DELIVERED Inventory Movement 


To reset/recalculate the current stock:
---------------------------------------

Available Stock: PO.Received - (SO.Dispatched + SO.Delivered)

Ordered Stock: PO.Ordered + PO.Confirmed + PO.Shipped + PO.Customs + PO.Cleared

Booked Stock: SO.Processing

Projected Stock: Available Stock + Ordered stock - Booked Stock (calculated field)




Triggers
--------


