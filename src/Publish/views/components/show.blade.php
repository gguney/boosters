<div class="panel panel-default">
	<div class="panel-heading">
		<h4>
			<strong>{{$DM->getShowName()}}</strong> Show
		</h4>
	</div>
	<div class="panel-body">
		@component('vendor.boosters.bodies.show', ['DM'=>$DM, 'item'=>$item])
		@endcomponent
	</div>
</div>
