<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kue</title>
</head>
<body>
    <h1>Daftar Kue 🎂</h1>
    
    @if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
    @endif

    <p><a href="{{ route('kues.create') }}">➕ Tambah Kue Baru</a></p>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kue</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kues as $kue)
            <tr>
                <td>{{ $kue->id }}</td>
                <td>{{ $kue->nama_kue }}</td>
                <td>Rp {{ number_format($kue->harga, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('kues.edit', $kue->id) }}">Edit</a> | 
                    <form action="{{ route('kues.destroy', $kue->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>