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


To get the stock at a given date, SUM received minus SUM delivered, filtering on created_at and warehouse



Purchase Order (PO)
-------------------
1 Draft
2 Ordered
3 Confirmed
4 Shipped
5 Customs
6 Cleared
7 Received

Purchase Order Invoices
-----------------------
DRAFT = 1;
ORDERED = 2;
CONFIRMED = 3;
SHIPPED = 4;		- trigger stock ordered, update Ordered Stock (add)
CUSTOMS = 5;
CLEARED = 6;
RECEIVED = 7;		- trigger stock in, update Available Stock (add), update Ordered Stock (deduct), create a RECEIVED Inventory Movement 
					- update the average buying price
PAID = 8;



Sales Order (SO)
----------------
Draft				

Blocked				- trigger stock out, update Blocked Stock (add) 
(verbally confirmed)

Booked				- trigger stock out, update Booked Stock (add), update Blocked Stock (deduct)
(payment received)

Dispatched			- trigger stock out, update Available Stock (deduct), update Booked Stock (deduct), create a DELIVERED Inventory Movement 
					- update the average selling price

