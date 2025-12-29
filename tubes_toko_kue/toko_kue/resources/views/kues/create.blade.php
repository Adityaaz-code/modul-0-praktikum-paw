// resources/views/kues/create.blade.php

@extends('layouts.app') 
@section('title', 'Tambah Kue')

@section('content')
    <h1>Tambah Kue Baru</h1>

    // PASTIKAN METHOD="POST"
    <form action="{{ route('kues.store') }}" method="POST"> 
        @csrf
        
        <label for="nama_kue">Nama Kue:</label>
        <input type="text" name="nama_kue" required> 
        <br>

        <label for="harga">Harga:</label>
        // TAMBAHKAN min="0" dan required
        <input type="number" name="harga" required min="0"> 
        <br>

        <button type="submit" class="btn-submit">Simpan</button>
    </form>
@endsection