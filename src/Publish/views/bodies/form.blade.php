@if(!isset($item))
    {!! Barista::open(['method'=>'POST', 'url'=>'/'.lcfirst($DM->getName()), 'files'=>true]) !!}
@else
    {!! Barista::open(['method'=>'PUT', 'url'=>'/'.lcfirst($DM->getName()), 'files'=>true, 'item'=>$item]) !!}
@endif

{!! Barista::buildFromDM($DM, (isset($item)) ? $item : null, $errors) !!}

@if(!isset($item))
    {!! Barista::close(['title'=>'Save' ]) !!}
@else
    {!! Barista::close(['title'=>'Update']) !!}
@endif


@if($DM->getName() == 'Articles')
    <script>
        CKEDITOR.replace('content');
    </script>
@endif
