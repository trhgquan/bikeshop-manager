@extends('content.brand.general')

@section('page-form')
Sua hang xe:<br/>
<form action="{{ route('brand.edit.view', $id) }}" method="POST">
Ten hang xe:<br/>
<input type="text" name="brand_name" value="Lorem"/><br/>
Mo ta hang xe:<br/>
<textarea name="brand_description" cols="30" rows="10">
Lorem ipsum dolor, sit amet consectetur adipisicing elit. 
Enim corporis aliquid quo obcaecati. 
Fuga repellendus rem reiciendis quia, aspernatur atque saepe, 
fugiat soluta aliquam sapiente maxime quis delectus? Soluta, quia?
</textarea>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<button type="submit">Sua</button>
</form>


Xoa hang xe:<br/>
<form action="{{ route('brand.delete', $id) }}" method="POST">
Nhan vao nut nay la ban se xoa hang xe. Suy nghi ky chua?
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<button type="submit">Xoa</button>
</form>
@endsection