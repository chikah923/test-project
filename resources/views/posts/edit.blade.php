@extends('layouts.default')

@section('title', 'Ladies Forum - update')

@section('content')

<h1>
  Ladies Forum
  <a class="header-menu" href="/">Back</a>
</h1>
<h2>Edit Post</h2>
<form action="/posts/update/{{$post->id}}" method="post" enctype= "multipart/form-data">
  {{ csrf_field() }}
  <p>
    <input type="hidden" name="id" placeholder="number" value="{{ $post->id }}">
    <input name="name" placeholder="Your name" type="text" value="{{ old('name', $post->name) }}" />
  </p>
  <p>
    <textarea name="body" placeholder="Enter comments">{{ old('body', $post->body) }}</textarea>
    @if ($errors->has('body'))
    <span class="error">{{ $errors->first('body') }}</span>
    @endif
  </p>

    @foreach ($images as $image)
      @if ($post->id == $image->post_id)
      <img src="{{ asset('images/'. $image->image) }}" width="400" height="200">
      <a class="del" href="/posts/del/image/{{ $image->id }}">[delete image]</a>
      <br>
      @endif
    @endforeach

  <p>
    <label>Upload new image: </label>
    <input type="file" name ="featured_image[]" id="featured_image">
  </p>

  <p>
    <input type="submit" value="Update" />
  </p>
</form>
@endsection
