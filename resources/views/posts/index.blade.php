@extends('layouts.default')

@section('title', 'Ladies Forum')

@section('content')
<h1>Ladies Forum</h1>
<form action = "/posts"  method ="post" enctype= "multipart/form-data">
  {{ csrf_field() }}
  <p>
    <input type="text" name="name" placeholder="Your name" value= "{{ old('title') }}">
  </p>

  <p>
    <textarea name="body" placeholder="Enter comments">{{ old('body') }}</textarea>
    @if ($errors->has('body'))
    <span class="error">{{ $errors->first('body') }}</span>
    @endif
  </p>

  <p>
    <label>Select image to Upload: </label>
    <input type="file" name ="featured_image[]" multiple id="featured_image">
  </p>

  <p>
    <input type="submit" value="Post Comment">
  </p>
</form>

<ul>
  @forelse ($posts as $post)
  <li>
    {{ $post->name }} [{{ $post->created_at }}]
    <a class="del" href="/posts/del/{{ $post->id }}">[x]</a>
    <a class="edit" href="/posts/{{ $post->id }}">[edit]</a>
    <a class="edit" href="/posts/show/{{ $post->id }}">※</a>
  </li>
  <li>{!! nl2br(e($post->body)) !!}</li>

    @foreach ($images as $image)
      @if ($post->id == $image->post_id)
    <img src="{{ asset('images/'. $image->image) }}" width="400" height="200">
    <br>
      @endif
    @endforeach

  <hr />
  @empty
  <li>No posts yet</li>
  @endforelse
</ul>

<div class="paginate">
{{ $posts->links() }}
</div>

@endsection
