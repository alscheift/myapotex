@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-5">
    <div class="row">
        <div class="col-12">
            <div class="card border shadow-xs mb-4">
                <div class="card-header border-bottom pb-0">
                    <div class="d-sm-flex align-items-center mb-3">
                        <div>
                            <h6 class="font-weight-semibold text-lg mb-0">Sales Transaction</h6>
                            <p class="text-sm mb-sm-0">Record and process sales transaction</p>
                        </div>
                        <div class="ms-auto d-flex">
                            <div class="me-4">
                                <h6 class="font-weight-semibold text-m mb-0">ID Transaction: T0001</h6>
                            </div>
                            <a href="">
                                <button type="button" id="reset-transaction-btn" class="btn btn-sm btn-white btn-icon d-flex align-items-center mb-0 me-2">
                                    <span class="btn-inner--text">Reset‎ transaction</span>
                                </button>
                            </a>
                            <button type="button" id="add-item-btn" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0 me-2">
                                <span class="btn-inner--icon me-2">
                                    <i class="fa-solid fa-plus" style="color: #ffffff;"></i>
                                </span>
                                <span class="btn-inner--text">Add‎ item</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-secondary text-xs font-weight-semibold">No.</th>
                                    <th class="text-secondary text-xs font-weight-semibold">ID</th>
                                    <th class="text-secondary text-xs font-weight-semibold">Medicine Name</th>
                                    <th class="text-secondary text-xs font-weight-semibold">Quantity</th>
                                    <th class="text-secondary text-xs font-weight-semibold">Discount</th>
                                    <th class="text-secondary text-xs font-weight-semibold">Price</th>
                                    <th class="text-secondary text-xs font-weight-semibold">Subtotal</th>
                                    <th class="text-secondary text-xs font-weight-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-container">

                            </tbody>
                            <tr id="new-row" style="display: none;">
                                <td class="ps-4"></td>
                                <td class="medicine-id"></td>
                                <td>
                                    <input type="text" name="medicine_name[]" class="form-control autocomplete-medicine" data-discount="" data-price="" required>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="quantity form-control" required>
                                </td>
                                <td class="discount"></td>
                                <td class="price"></td>
                                <td class="subtotal"></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger delete-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="discount">Discount</label>
                            <input type="number" name="discount" class="form-control" id="discount" value="" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total">Total</label>
                            <input type="number" name="total" class="form-control" id="total" value="" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <button type="submit" id="pay-btn" class="btn btn-dark me-2">Pay</button>
                        <button type="button" class="btn btn-secondary me-2">Cancel</button>
                        <button type="button" class="btn btn-primary">Print Payment Receipt</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cash">Cash</label>
                            <input type="number" name="cash" class="form-control" id="cash" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="change">Change</label>
                            <input type="number" name="change" class="form-control" id="change" value="" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        // Add Item Button
        $('#add-item-btn').click(function () {
            const newRow = $('#new-row').clone().removeAttr('id').show();
            newRow.find('td:first').text($('#items-container tr').length-1);
            newRow.find('input[name="medicine_name[]"]').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: '{{ route("sales.search") }}',
                        dataType: 'json',
                        data: {
                            q: request.term
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 3,
                select: function (event, ui) {
                    newRow.find('input[name="medicine_name[]"]').val(ui.item.label);
                    newRow.find('input[name="medicine_name[]"]').attr('data-id', ui.item.id);
                    newRow.find('input[name="medicine_name[]"]').attr('data-discount', ui.item.discount);
                    newRow.find('input[name="medicine_name[]"]').attr('data-price', ui.item.price);
                    newRow.find('td:nth-child(2)').text(ui.item.id);
                    newRow.find('td:nth-child(5)').text(ui.item.discount);
                    newRow.find('td:nth-child(6)').text(ui.item.price);
                    updateSubtotal(newRow);
                }
            }).on("keyup", function () {
                const input = $(this);
                if (input.val().length >= 3) {
                    input.autocomplete("search", input.val());
                }
            });

            newRow.find('input[name="quantity[]"]').on('input', function () {
                updateSubtotal(newRow);
            });
            newRow.find('.delete-row').click(function () {
                $(this).closest('tr').remove();
                updateRowNumbers();
            });

            $('#items-container').append(newRow);
            updateRowNumbers();
        });

        // Delete Row Button
        $(document).on('click', '.delete-row', function () {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });

        // Add Row on Enter
        $(document).on('keydown', 'input[name="quantity[]"]', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                $('#add-item-btn').click();
            }
        });

        // Update row numbers
        function updateRowNumbers() {
            $('#items-container tr').each(function (index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        // Update Subtotal
        function updateSubtotal(row) {
            const quantity = row.find('input[name="quantity[]"]').val();
            const discount = row.find('input[name="medicine_name[]"]').data('discount');
            const price = row.find('input[name="medicine_name[]"]').data('price');
            const subtotal = quantity * price * (1 - discount);
            row.find('td:nth-child(7)').text(subtotal);
        }

        $(document).on('input', 'input[name="quantity[]"]', function () {
            // Menghitung discount dan total
            let discount = 0;
            let total = 0;
            $('#items-container tr').each(function () {
                const quantity = $(this).find('input[name="quantity[]"]').val();
                const price = $(this).find('input[name="medicine_name[]"]').data('price');
                const itemDiscount = $(this).find('input[name="medicine_name[]"]').data('discount');
                const subtotal = quantity * price * (1 - itemDiscount);
                total += subtotal;
                discount += quantity * price * itemDiscount;
            });

            // Mengupdate nilai discount dan total
            $('#discount').val(discount);
            $('#total').val(total);
        });

        // Menghitung kembalian
        $(document).on('input', '#cash', function () {
            const cash = $(this).val();
            const total = parseFloat($('#total').val());
            const change = cash - total;

            $('#change').val(change);
        });

        $('#pay-btn').click(function () {
            // Mengambil data transaksi dari form
            const cash = $('#cash').val();
            const discount = $('#discount').val()
            const total = $('#total').val();
            const change = $('#change').val();

            // Validasi jika cash < total
            if (Number(cash) < Number(total)) {
                alert('Insufficient cash amount. Please enter a higher value.');
                return; // Menghentikan eksekusi fungsi jika kondisi tidak terpenuhi
            }

            // Membuat objek FormData dari form
            const formData = new FormData();
            formData.append('cash', cash);
            formData.append('discount', discount);
            formData.append('total', total);
            formData.append('change', change);

            // Setup CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Mengirim data penjualan ke server menggunakan AJAX
            $.ajax({
                url: '{{ route("sales.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Mengambil ID penjualan dari respons
                    const saleId = response.sale_id;

                    // Mengambil data detail penjualan dari form
                    const medicineIds = $('#items-container .medicine-id').map(function (_,el) {
                        return el.textContent;
                    });
                    const quantities = $('#items-container .quantity').map(function (_,el) {
                        return Number(el.value);
                    });
                    const prices = $('#items-container .price').map(function (_,el) {
                        return Number(el.textContent);
                    });
                    const discounts = $('#items-container .discount').map(function (_,el) {
                        return Number(el.textContent);
                    });
                    const subtotals = $('#items-container .subtotal').map(function (_,el) {
                        return Number(el.textContent);
                    });

                    // Membuat objek FormData untuk data detail penjualan
                    const detailFormData = new FormData();
                    detailFormData.append('sale_id', saleId);
                    for (let i = 0; i < medicineIds.length; i++) {
                        detailFormData.append('medicine_id[]', medicineIds[i]);
                        detailFormData.append('quantity[]', quantities[i]);
                        detailFormData.append('price[]', prices[i]);
                        detailFormData.append('discount[]', discounts[i]);
                        detailFormData.append('subtotal[]', subtotals[i]);
                    }

                    // Mengirim data detail penjualan ke server menggunakan AJAX
                    $.ajax({
                        url: '{{ route("detailsales.store", ":sale_id") }}'.replace(':sale_id', saleId),
                        method: 'POST',
                        data: detailFormData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            // Mengupdate halaman atau menampilkan pesan sukses
                            alert('Transaction details added successfully!');
                            location.reload();
                        },
                        error: function (error) {
                            alert('Failed to add transaction details. Please try again.');
                        }
                    });
                },
                error: function (error) {
                    alert('Failed to add transaction. Please try again.');
                }
            });
        });

        $('#reset-transaction-btn').click(function(){
            const parentElement = document.getElementById("items-container");
            while (parentElement.firstChild) {
                parentElement.removeChild(parentElement.firstChild);
            }
        });
    });
</script>
@endsection