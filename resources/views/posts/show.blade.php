@extends('layouts.default')

@section('title', 'Ladies Forum')

@section('content')
    <h1>Ladies Forum
    <a href= "{{ url('/') }}" class= "header-menu">Back</a>
    </h1>
    
    <p> {{ $post->name }} [{{ $post->created_at }}]</p>
    <p>{!! nl2br(e($post->body)) !!}  </p>

    <h2>＊Comments＊</h2> 
    
      <ul>
         @forelse ($post->comments as $comment)
          <li>
          {{ $comment->body }} 
          <a href="{{ action('CommentsController@destroy', $comment) }}" class="del">[x]</a>
          {{ (var_dump($comment)) }}
          </li>
          <hr>
          @empty
          <li>No comments yet</li>
          @endforelse
      </ul>


     <form action = "{{ action('CommentsController@store', $post) }}" method ="post">
       {{ csrf_field() }}

        <p>
        <input type="hidden" name="post_id" placeholder="number" value= "{{ $post->id }}">
      
        <input type="text" name="body" placeholder="Comment" value= "{{ old('body') }}">
        @if ($errors->has('body'))
        <span class="error">{{ $errors->first('body') }}</span>
        @endif
        </p>
        
        <p>
        <input type="submit" value="Post Comment">
        </p>
     
     </form>

@endsection