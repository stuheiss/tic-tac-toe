@extends('layout')

@section('title', 'Tic Tac Toe')

@section('content')

<h1>Tic Tac Toe</h1>

<a href='/play'>Start a new 2 player game</a><br>
<a href='/play/easy'>Start a new easy ai vs human game</a><br>
<a href='/play/medium'>Start a new medium ai vs human game</a><br>
<a href='/play/hard'>Start a new hard ai vs human game</a><br>

@if (session('status'))
    <div class="alert alert-danger">
        {{ session('status') }}
    </div>
@endif

@if($board)
  @if($winner)
    <div class='alert alert-success'>WINNER: {{ $winner }}!!!</div>
  @endif

  @if(!$winner && !$playsleft)
    <div class='alert alert-primary'>TIE GAME!!!</div>
  @endif

  @if($ai)
    <div>AI: {{ $ai }}</div>
  @endif
  <div>Player: {{ $player }}</div>
  <div>
    @foreach($board as $row)
      <div>
      @foreach($row as $cell)
        <div style='display:inline-block'>
          <form id='move' method='post' action='/move'>
          @csrf
          <input type='hidden' name='move' value="{{ $cell }}"></input>
          <button type='submit' style='width:50px; height:50px'>{{ $cell }}</button>
          </form>
        </div>
      @endforeach
      </div>
    @endforeach
  </div>
@endif

@endsection