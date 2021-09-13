Inventory
=========

Purchase Order (PO)
-------------------
Draft
Ordered
Confirmed
Shipped
Customs
Cleared
Received

Sales Order (SO)
----------------
Draft
Processing
Dispatched
Delivered



Available Stock: PO.Received - (SO.Dispatched + SO.Delivered)

Ordered Stock: PO.Ordered + PO.Confirmed + PO.Shipped + PO.Customs + PO.Cleared

Booked Stock: SO.Processing



Triggers
--------

1. The Product model should trigger an entry in the 'Inventory' table when a new product is created

2. The Product model should trigger corresponding delete when a product is 'deleted' in the 'Inventory' and 'Inventory Movement' table

2. The PO and SO models should have triggers when the status changes to the ones listed above, and update these columns in the 'Inventory' table.

3. The PO and SO Models should have triggers for each status change, creating an entry in the 'Inventory Movement' table
