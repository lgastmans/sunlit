Inventory
=========

table 'Inventory' is a one-to-one relationship with products, holds the 3 different stock values
table 'Inventory Movements' tracks the movement of stock from both the Purchase Orders and Sales Orders

Inventory table
---------------
id
warehouse_id
product_id
model_name 		POR|SOR (Purchase Order or Sales Order)
model_id		references the id of POR or SOR
model_status	references the status of the model
quantity
date_created
user_id
soft-deletes



Purchase Order (PO)
-------------------
Draft			
Ordered			
Confirmed		- trigger stock ordered, update Ordered Stock (add)
Shipped			
Customs			
Cleared			
Received		- trigger stock in, update Available Stock (add), update Ordered Stock (deduct)

Sales Order (SO)
----------------
Draft
Processing		- trigger stock out, update Booked Stock (add)
Dispatched		- trigger stock out, update Available Stock (deduct), update Booked Stock (deduct)
Delivered


To reset/recalculate the current stock:
---------------------------------------

Available Stock: PO.Received - (SO.Dispatched + SO.Delivered)

Ordered Stock: PO.Ordered + PO.Confirmed + PO.Shipped + PO.Customs + PO.Cleared

Booked Stock: SO.Processing

Projected Stock: Available Stock - Booked Stock (calculated field)




Triggers
--------


