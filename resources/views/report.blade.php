@extends('layouts.main')

@section('content')

  <div class="d-sm-flex align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Report Transaction</h1>
  </div>

  {{-- <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                Earnings (Monthly)</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-calendar fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> --}}

  <div class="card shadow p-4">

    <div class="row my-3">
      <div class="col-md-2">
        <select id="search-type" class="form-control">
          <option value="">Filter By</option>
          <option value="date">Date</option>
          <option value="merchant_name">Merchant Name</option>
          <option value="payment_status">Payment Status</option>
          <option value="outlet_name">Outlet Name</option>
        </select>
      </div>
      <div class="col-md-5">
        <input type="text" id="input-search" class="form-control" placeholder="Seach here">
        <select id="payment-type-search" class="form-control">
          <option value="">Choose Type</option>
          <option value="Paid">Paid</option>
          <option value="Not Paid">Not Paid</option>
        </select>
        <div class="row" id="date-search">
          <div class="col-5">
            <input type="date" id="date-search-start" class="form-control">
          </div>
          <div class="col-2 d-flex justify-content-center align-items-center">To</div>
          <div class="col-5">
            <input type="date" id="date-search-end" class="form-control">
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <button id="search-button" class="btn btn-info">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </div>
      <div class="col-md-3 text-right">
        <button class="btn btn-info" id="exportReport">
          <i class="fas fa-download fa-sm mr-1"></i> Export
        </button>
      </div>
    </div>

    <table class="table table-responsive table-hover mt-3">
      <thead>
        <tr>
          <th>No</th>
          <th>Merchant</th>
          <th>Outlet</th>
          <th>Transaction Time</th>
          <th>Staff</th>
          <th>Pay Amount</th>
          <th>Payment Type</th>
          <th>Customer</th>
          <th>Tax</th>
          <th>Change Amount</th>
          <th>Total Amount</th>
          <th>Payment Status</th>
        </tr>
        {{-- <tr>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
          <td><input type="text" class="input-filter" /></td>
        </tr> --}}
      </thead>
      <tbody id="transactions-list"></tbody>
    </table>

    <div class="d-flex justify-content-end">
      <div class="pagination">
        <button class="btn btn-outline-info btn-sm mr-2" id="prev-page">Previous</button>
        <button class="btn btn-outline-info btn-sm" id="next-page">Next</button>
      </div>
    </div>

  </div>

  <script>
    $(document).ready(function() {
      $("#date-search").hide();
      $("#payment-type-search").hide();

      let offset = 0;
      let limit = 10;
      let searchType = '';
      let searchValue = '';

      loadData();

      function loadData() {
        $.ajax({
          url: '/get-transactions',
          method: "GET",
          dataType: "json",
          data: {
            offset: offset,
            limit: limit,
            searchType: searchType,
            searchValue: searchValue
          },
          success: function (data) {
            let transcList = data.transactions;
            $("#transactions-list").empty();
            let no = 1;
            let nf = new Intl.NumberFormat('de-DE');

            if (transcList.length <= 0) {
              let emptyRow = $("<tr>");
              emptyRow.append($("<td class='text-center font-weight-bold py-5' colspan='12'>").text("No data available"));
              $("#transactions-list").append(emptyRow);
            } else {
              $.each(transcList, function (index, transc) {
                let row = $("<tr>");
                row.append($("<td>").text(no++));
                row.append($("<td>").text(transc.merchant_name));
                row.append($("<td>").text(transc.outlet_name));
                row.append($("<td>").text(transc.transaction_time));
                row.append($("<td>").text(transc.staff));
                row.append($("<td>").text(nf.format(transc.pay_amount)));
                row.append($("<td>").text(transc.payment_type));
                row.append($("<td>").text(transc.customer_name));
                row.append($("<td>").text(nf.format(transc.tax)));
                row.append($("<td>").text(nf.format(transc.change_amount)));
                row.append($("<td>").text(nf.format(transc.total_amount)));
                row.append($("<td>").html(transc.payment_status === 'Paid' ? '<span class="text-success">Paid</span>' : '<span class="text-danger">Not Paid</span>'));
                $("#transactions-list").append(row);
              });
            }

            updatePaginationButtons();
          },
          error: function (error) {
            console.error(error);
          }
        });
      }

      function updatePaginationButtons() {
        $("#prev-page").prop("disabled", offset <= 0);
        $("#next-page").prop("disabled", data.transactions.length < limit);
      }

      $("#prev-page").on("click", function () {
        offset -= limit;
        loadData();
        updatePaginationButtons();
      });

      $("#next-page").on("click", function () {
        offset += limit;
        loadData();
        updatePaginationButtons();
      });


      $("#search-type").change(function() {
        searchType = $(this).val();
        let selectedValue = $(this).val();
        switch (selectedValue) {
          case "date":
            $("#input-search").hide().val('');
            $("#payment-type-search").hide().val('');
            $("#date-search").show();
            break;
          case "payment_status":
            $("#input-search").hide().val('');
            $("#date-search").hide();
            $("#date-search-start").val('');
            $("#date-search-end").val('');
            $("#payment-type-search").show();
            break;
          default:
            $("#date-search").hide();
            $("#date-search-start").val('');
            $("#date-search-end").val('');
            $("#payment-type-search").hide().val('');
            $("#input-search").show();
            break;
        }
      });


      $("#search-button").on("click", function () {
        searchType = $("#search-type").val();
        switch (searchType) {
          case "date":
            let startDate = $("#date-search-start").val();
            let endDate = $("#date-search-end").val();
            searchValue = startDate + " - " + endDate;
            break;
          case "payment_status":
            searchValue = $("#payment-type-search").val();
            break;
          default:
            searchValue = $("#input-search").val();
            break;
        }

        offset = 0;
        loadData();
        updatePaginationButtons();
      });

    });
  </script>
  
@endsection