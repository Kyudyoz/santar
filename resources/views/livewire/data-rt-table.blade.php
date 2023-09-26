<div>
    <div class="col-xl-12 col-xxl-12 d-flex">
        <div class="w-100">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <input type="text" class="form-control w-25" wire:model.live="search" placeholder="Cari RT...">
                        </div>
                        <table class="table table-hover my-0 text-center">
                            @if ($rts->count())
                            <thead>
                                <tr>
                                    <th>RT</th>
                                    <th>Nama Ketua RT</th>
                                    <th>Lihat Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rts as $rt)
                                <tr>
                                    <td>{{ $rt->nama_rt }}</td>
                                    <td>{{ $rt->nama_ketua }}</td>
                                    <td>
                                        <a href="/lihatDetailRt/{{ $rt->id }}" class="btn btn-primary">
                                            <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <hr>
                                    <p class="text-center">RT tidak ditemukan</p>
                            </tbody>
                            @endif
                        </table>
                        <div class="d-flex justify-content-end mx-2 mt-2">
                            {{ $rts->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
