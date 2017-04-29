<div class="card">
    <div class="card-block">
        <div class="card-heading">
            <div class="row">
                <div class="col">
                    <h3>
                        <strong>{{ $DM->getShowName() }}</strong> Index
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    {!! Barista::button( 'Add New '.$DM->getModelName(), 'Add New '.$DM->getModelName(), ['class'=>'btn btn-info pull-left', 'data-toggle' => 'modal', 'data-target' => '#modal', 'data-DM'=>camel_case($DM->getName()), 'data-action'=>'create' ] ) !!}
                </div>
            </div>
        </div>
        <div class="card-block">
            @component('vendor.boosters.bodies.table', ['DM'=>$DM, 'items'=>$items])
            @endcomponent
        </div>
    </div>
</div>
@component('vendor.boosters.components.modal')
@endcomponent
<script src="/js/booster.js"></script>
