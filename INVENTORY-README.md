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