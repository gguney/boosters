<div class="panel panel-default">
	<div class="panel-heading">
		<h4>
			<strong>{{ $DM->getShowName() }}</strong>{{ (!isset($item)) ? ' Create' : ' Edit' }}
		</h4>
	</div>
	<div class="panel-body">
		@component('vendor.boosters.bodies.form', ['DM'=>$DM, 'item'=>$item])
		@endcomponent
	</div>
</div>
