@extends('content.brand.general')

@section('page-form')
Them mot hang xe moi:<br/>
<form action="">
Ten hang xe:<br/><input type="text" name="brand_name"/><br/>
Mo ta hang xe:<br/><textarea name="brand_description" cols="30" rows="10"></textarea>
<button type="submit">Them</button>
</form>
@endsection