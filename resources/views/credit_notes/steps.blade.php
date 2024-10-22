<div class="row">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10 col-sm-11">
                <div class="horizontal-steps mt-4 mb-4 pb-5" id="tooltip-container">
                    <div class="horizontal-steps-content">
                        <div class="step-item @if ($order->status == 1) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $order->display_created_at }}">Draft</span>
                        </div>
                        <div class="step-item @if ($order->status == 2) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $order->display_pending_at }}">Confirmed</span>
                        </div>

                    </div>
                    <div class="process-line"
                    @switch($order->status)
                        @case(1)
                            style="width:0%;"
                            @break
                        @case(2)
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