@extends('layout')

@section('title', 'Tic Tac Toe')

@section('content')
<div style='margin-left:50px; margin-top:50px;'>
<h1>Tic Tac Toe</h1>

<div>
  Start a new game:<br>
<ul>
<li>
  <a href='/play'>Human vs Human</a><br>
</li>
<li>
  <a href='/play/easy'>Easy AI vs Human</a><br>
</li>
<li>
  <a href='/play/medium'>Medium AI vs Human</a><br>
</li>
<li>
  <a href='/play/hard'>Hard AI vs Human</a>
</li>
</ul>
</div>

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
  <div>Player: {{ $player }} is up</div>
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
</div>
@endsection