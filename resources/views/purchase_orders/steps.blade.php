<div class="row">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10 col-sm-11">
                <div class="horizontal-steps mt-4 mb-4 pb-5" id="tooltip-container">
                    <div class="horizontal-steps-content">
                        <div class="step-item @if ($purchase_order->status == 2) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_ordered_at }}">Ordered</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 3) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_confirmed_at }}">Confirmed</span>
                        </div>
                        {{-- <div class="step-item @if ($purchase_order->status == 4) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_shipped_at }}">Shipped</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 5) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_customs_at }}">Customs</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 6) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_cleared_at }}">Cleared</span>
                        </div> --}}
                        <div class="step-item @if ($purchase_order->status == 7) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_received_at }}">Received</span>
                        </div>
                    </div>
                    <div class="process-line"
                    @switch($purchase_order->status)
                        @case(2)
                            style="width:0%;"
                            @break
                        @case(3)
                            style="width:20%;"
                            @break
                        @case(4)
                            style="width:40%;"
                            @break
                        @case(5)
                            style="width:60%;"
                            @break
                        @case(6)
                            style="width:80%;"
                            @break
                        @case(7)
                            style="width:100%;"
                            @break
                        @default
                        style="width:0%;"
                    @endswitch
                    ></div>
                </div>
            </div>
        </div>
    </div>
</div>