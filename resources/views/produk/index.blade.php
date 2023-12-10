@extends('layouts.master')

@section('title')
    Daftar Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm('{{ route('produk.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <button onclick="deleteSelected('{{ route('produk.delete_selected') }}')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                    <button  onclick="cetakBarcode('{{ route('produk.cetak_barcode') }}')"  type ="button" class="btn btn-info btn-xs btn-flat" ><i class="fa fa-barcode"></i> Cetak Barcode</button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-produk">
                    @csrf
                    <table class="table table-stiped table-bordered">
                        <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Diskon</th>
                            <th>Stok</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBarcode" tabindex="-1" role="dialog" aria-labelledby="modalBarcode" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post" class="form-barcode">
            @csrf
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button onclick="printBarcode('{{ route('produk.cetak_barcode') }}')" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

@includeIf('produk.form')
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
    let table;
    let selectedProducts = [];

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            autoWidth: false,
            ajax: {
                url: '{{ route('produk.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'nama_kategori'},
                {data: 'merk'},
                {data: 'harga_beli'},
                {data: 'harga_jual'},
                {data: 'diskon'},
                {data: 'stok'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        return;
                    });
            }
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_produk]').focus();
    }

  

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_produk]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=nama_produk]').val(response.nama_produk);
                $('#modal-form [name=id_kategori]').val(response.id_kategori);
                $('#modal-form [name=merk]').val(response.merk);
                $('#modal-form [name=harga_beli]').val(response.harga_beli);
                $('#modal-form [name=harga_jual]').val(response.harga_jual);
                $('#modal-form [name=diskon]').val(response.diskon);
                $('#modal-form [name=stok]').val(response.stok);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function deleteSelected(url) {
        if ($('input:checked').length > 1) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, $('.form-produk').serialize())
                    .done((response) => {
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        } else {
            alert('Pilih data yang akan dihapus');
            return;
        }
    }

    function qtyBarcode(url) {
        var $formBarcode = $('.form-barcode');
        $formBarcode.empty();

        $('#modalBarcode').modal('show');
        for (var i = 0; i < selectedProducts.length; i++) {
        // Membuat elemen input tersembunyi baru
            var $formGroup = $('<div>').addClass('form-group row');

            // Membuat label dengan class 'col-lg-2 col-lg-offset-1 control-label'
            var $label = $('<label>').attr({
                for: selectedProducts[i].name + i,
                class: 'col-lg-2 col-lg-offset-1 control-label'
            }).text(selectedProducts[i].name);

            // Membuat div untuk input dengan class 'col-lg-6'
            var $inputDiv = $('<div>').addClass('col-lg-6');

            // Membuat elemen input tersembunyi baru
            var $input = $('<input>').attr({
                type: 'number',
                name: 'quantity[]',
                id: 'input_' + i,
                class: 'form-control',
                required: true,
                autofocus: i === 0 // Fokus pada input pertama
            });

            // Menambahkan elemen input ke dalam div input
            $inputDiv.append($input);

            // Menambahkan label dan div input ke dalam div utama
            $formGroup.append($label, $inputDiv);

            // Menambahkan div utama ke dalam formulir
            $formBarcode.append($formGroup);
        }

        

        
            
    
    }

    function printBarcode(url) {
        var $formBarcode = $('.form-barcode');

        var nilaiQuantity = $formBarcode.find('input[name="quantity[]"]').map(function() {
            return $(this).val();
        }).get();

        // Menampilkan nilai-nilai tersebut
        console.log('Nilai dari nama_produk:', nilaiQuantity);
        console.log("jumlah selected produk : " , selectedProducts.length)
        nilaiQuantity.forEach(function(quantity) {
            console.log(quantity)
            $('<input>').attr({
                type: 'hidden',
                name: 'quantity[]',
                value: quantity
            }).appendTo('.form-produk');
        });
        selectedProducts = []
        $('.form-produk')
        .attr('target', '_blank')
        .attr('action', url)
         .submit();
        //  console.log('Data formulir yang dikirim:', $('.form-produk').serialize());

    }
    

    function cetakBarcode(url) {

        if ($('input:checked').length < 1) {
            alert('Pilih data yang akan dicetak');
            return;
        } else if ($('input:checked').length < 3) {
            alert('Pilih minimal 3 data untuk dicetak');
            return;
        } else {
            $('input[name="id_produk[]"]:checked').each(function() {
            // Mendapatkan nilai (ID) dari setiap checkbox yang terpilih
            var productId = $(this).val();
            // Mendapatkan nama produk
            var productName = $(this).closest('tr').find('td:eq(3)').text();

            // Menambahkan informasi produk ke dalam array selectedProducts
            selectedProducts.push({
                id: productId,
                name: productName
            });
        });
        qtyBarcode(selectedProducts, url)

        }
    }
</script>
@endpush