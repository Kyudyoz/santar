<div>
    <div class="col-xl-12 col-xxl-12 d-flex">
        <div class="w-100">
            <div class="row">
                <div class="col-sm-12 table">
                    <div class="container">
                        <div class="card-header my-2">
                            <input type="text" class="form-control w-25" wire:model.live="search" placeholder="Cari user...">
                        </div>
                        <table class="table table-hover my-0 text-center">
                            @if ($users->count())
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>RT</th>
                                    <th>Alamat</th>
                                    <th>Nomor Telepon</th>
                                    <th>Status</th>
                                    <th>Lihat Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                    <td>{{ $user->nama }}</td>
                                    <td>{{ $user->rt->nama_rt }}</td>
                                    <td>{{ $user->alamat }}</td>
                                    <td>
                                        @if ($user->no_hp)
                                        <a href="https://wa.me/{{ $user->no_hp }}" class="btn btn-success">
                                            <i class="fa-brands fa-whatsapp fa-xl"></i> +{{ $user->no_hp }}
                                        </a>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $user->role }}</td>
                                    <td>
                                        <a href="/lihatDetail/{{ Crypt::encrypt($user->id) }}" class="btn btn-primary">
                                            <i class="align-middle fa-solid fa-eye fa-xl text-dark"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <hr>
                                    <p class="text-center">User tidak ditemukan</p>
                            </tbody>
                            @endif
                        </table>
                        <div class="d-flex justify-content-end mx-2 mt-2">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
