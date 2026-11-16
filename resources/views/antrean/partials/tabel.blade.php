<table>
    <thead>
    <tr>
        <th>Poli</th>
        <th>Sedang Dipanggil</th>
        <th>Berikutnya</th>
        <th>Menunggu</th>
        <th>Total Hari Ini</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($ringkasan as $baris)
        <tr>
            <td>{{ $baris['poli'] }}</td>
            <td>{{ $baris['nomor_dipanggil'] ? sprintf('%s-%03d', $baris['kode'], $baris['nomor_dipanggil']) : '-' }}</td>
            <td>{{ $baris['nomor_berikutnya'] ? sprintf('%s-%03d', $baris['kode'], $baris['nomor_berikutnya']) : '-' }}</td>
            <td>{{ $baris['jumlah_menunggu'] }}</td>
            <td>{{ $baris['total_hari_ini'] }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="muted">Belum ada data poli. Jalankan seeder terlebih dahulu.</td></tr>
    @endforelse
    </tbody>
</table>
