@extends('layouts.default')

@section('title', 'Ladies Forum - update')

@section('content')
    <h1>Ladies Forum
     <a href="/" class="header-menu">Back</a>
    </h1> 
    <h2>Edit Post</h2>
       <form action = "/posts/update/{{$post->id}}" method ="post">
       
       {{ csrf_field() }}

        <p>
        <input type="text" name="name" placeholder="Your name" value= "{{ old('name', $post->name) }}">
        </p>

        <p>
        <textarea name="body" placeholder="Enter comments">{{ old('body', $post->body) }}</textarea>
        @if ($errors->has('body'))
        <span class="error">{{ $errors->first('body') }}</span>
        @endif
        </p>

        <p>
        <input type="submit" value="Update">
        </p>
      </form>
@endsection
