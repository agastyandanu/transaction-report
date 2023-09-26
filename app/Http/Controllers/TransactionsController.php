<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        $searchType = $request->input('searchType', '');
        $searchValue = $request->input('searchValue', '');

        $query = Transactions::select('transactions.*', 'merchants.merchant_name as merchant_name', 'outlets.outlet_name as outlet_name')
            ->leftJoin('merchants', 'transactions.merchant_id', '=', 'merchants.id')
            ->leftJoin('outlets', 'transactions.outlet_id', '=', 'outlets.id');

        if ($searchType === 'merchant_name') {
            $query->where('merchants.merchant_name', 'LIKE', '%' . $searchValue . '%');
        } else if ($searchType === 'outlet_name') {
            $query->where('outlets.outlet_name', 'LIKE', '%' . $searchValue . '%');
        } else if ($searchType === 'payment_status') {
            $query->where('transactions.payment_status', 'LIKE', '%' . $searchValue . '%');
        } else if ($searchType === 'date') {
            $dates = explode(' - ', $searchValue);
            if (count($dates) === 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->whereBetween('transactions.transaction_time', [$startDate, $endDate]);
            }
        } else if ($searchType === null && $searchValue !== null) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('merchants.merchant_name', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('outlets.outlet_name', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.transaction_time', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.payment_type', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.pay_amount', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.customer_name', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.tax', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.change_amount', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.total_amount', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('transactions.payment_status', 'LIKE', '%' . $searchValue . '%');
            });
        }
        $transactions = $query->skip($offset)->take($limit)->get();
        return response()->json(['transactions' => $transactions]);
    }

    public function exportDataCSV()
    {

    }

}

// INSERT INTO `transactions` (`id`, `merchant_id`, `outlet_id`, `transaction_time`, `staff`, `pay_amount`, `payment_type`, `customer_name`, `tax`, `change_amount`, `total_amount`, `payment_status`, `created_at`, `updated_at`) VALUES
//     (NULL, '1', '1', '2023-09-26 09:23:14.000000', 'Wandi', '11000000', 'Cash', 'Michelle', '100000', '0', '11100000', 'Paid', '2023-09-22 09:50:25', NULL),
//     (NULL, '2', '4', '2023-09-26 08:54:34.000000', 'Winda', '12000000', 'Debit', 'Andre', '100000', '0', '12100000', 'Paid', '2023-09-21 09:54:25', NULL),
//     (NULL, '3', '3', '2023-09-26 05:45:10.000000', 'Lukman', '13000000', 'Credit Card', 'Sutomo Widoyo', '100000', '0', '13100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '1', '2023-09-24 01:12:35.000000', 'Ando', '12000000', 'Cash', 'Verent', '100000', '0', '12100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '5', '2', '2023-09-25 09:44:24.000000', 'Ando', '11000000', 'GoPay', 'Widyaningrum', '100000', '0', '11100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '3', '2', '2023-09-23 09:14:41.000000', 'Putri', '13000000', 'Cash', 'Dimas Brata', '100000', '0', '13100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '4', '2023-09-26 11:04:59.000000', 'Cahyani', '14000000', 'Bank Transfer', 'Cahyadi', '100000', '0', '14100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '2', '2023-09-26 12:27:33.000000', 'Cahyani', '11000000', 'Bank Transfer', 'Intan Situmorang', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '1', '3', '2023-09-24 04:35:54.000000', 'Albert', '13000000', 'Cash', 'Ciptadi', '100000', '0', '13100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '5', '2', '2023-09-24 04:49:09.000000', 'Wandi', '11000000', 'Debit', 'Jefri Sulaiman', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '1', '1', '2023-09-10 04:40:22.000000', 'Winda', '11000000', 'Debit', 'Andika Brata', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '5', '1', '2023-09-24 11:42:33.000000', 'Cahyani', '11000000', 'Credit Card', 'Widyo', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '3', '3', '2023-09-24 11:45:47.000000', 'Lukman', '11000000', 'Debit', 'Santika Ibna', '100000', '0', '11100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '1', '2023-09-26 12:56:45.000000', 'Ando', '11000000', 'Bank Transfer', 'Fero Almando', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '1', '2023-09-24 21:50:28.000000', 'Putri', '11000000', 'Debit', 'Jajang Suherman', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '3', '2', '2023-09-26 20:14:29.000000', 'Winda', '12000000', 'Credit Card', 'Andito Sumitro', '100000', '0', '12100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '1', '4', '2023-09-24 04:42:21.000000', 'Winda', '10000000', 'Debit', 'Fahrul Ahmad', '100000', '0', '10100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '3', '2023-09-10 19:51:56.000000', 'Lukman', '11000000', 'Cash', 'Sri Rahmitullah', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '2', '2023-09-11 20:19:22.000000', 'Putri', '11000000', 'OVO', 'Deffi Amanda', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '4', '2023-09-24 04:21:23.000000', 'Ando', '11000000', 'Credit Card', 'Yanto Basriala', '100000', '0', '11100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '4', '2023-09-24 21:18:28.000000', 'Cahyani', '11000000', 'Debit', 'Wawan', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '3', '4', '2023-09-12 20:43:21.000000', 'Ando', '11000000', 'Debit', 'Kumala Sari', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '2', '2023-09-26 04:11:59.000000', 'Putri', '11000000', 'Bank transfer', 'Sinar Dunia', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '3', '3', '2023-09-12 17:12:12.000000', 'Lukman', '11000000', 'Cash', 'Montis T.', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '4', '2023-09-24 04:14:01.000000', 'Ando', '11000000', 'Credit Card', 'Dana Saputro', '100000', '0', '11100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '3', '2023-09-24 16:50:22.000000', 'Lukman', '16000000', 'GoPay', 'Ahmad Julianto', '100000', '0', '16100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '1', '3', '2023-09-12 20:59:21.000000', 'Putri', '12000000', 'Debit', 'Wahyu Andika Putra', '100000', '0', '12100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '1', '3', '2023-09-26 15:57:20.000000', 'Putri', '14000000', 'OVO', 'Hafizh Maulana', '100000', '0', '14100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '1', '2', '2023-09-22 17:57:10.000000', 'Lukman', '11000000', 'Cash', 'Maulana Kubika', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '3', '2', '2023-09-24 20:25:21.000000', 'Ando', '11000000', 'OVO', 'Terios Susanto', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '5', '4', '2023-09-26 09:17:29.000000', 'Cahyani', '11000000', 'OVO', 'Richard Sitompul', '100000', '0', '11100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '3', '2', '2023-09-26 10:37:33.000000', 'Winda', '15000000', 'Bank Transfer', 'Heri Saputra', '100000', '0', '15100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '5', '1', '2023-09-25 15:50:20.000000', 'Wandi', '11000000', 'Credit Card', 'Hatmadjie', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '2', '2023-09-21 20:20:20.000000', 'Winda', '11000000', 'Debit', 'Komoria Adrus', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '4', '2023-09-22 21:51:20.000000', 'Cahyani', '11000000', 'Credit Card', 'Melanie', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '2', '4', '2023-09-22 09:18:20.000000', 'Ando', '11000000', 'Cash', 'Xiao Lee', '100000', '0', '11100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '1', '2023-09-23 12:59:20.000000', 'Cahyani', '11000000', 'Credit Card', 'Gatot Subroto', '100000', '0', '11100000', 'Not Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '3', '2023-09-24 10:12:20.000000', 'Putri', '12000000', 'OVO', 'Wanda Michelle', '100000', '0', '12100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '5', '1', '2023-09-12 17:34:20.000000', 'Lukman', '13000000', 'GoPay', 'Albert Instant', '100000', '0', '13100000', 'Paid', '2023-09-26 09:54:25', NULL),
//     (NULL, '4', '1', '2023-09-11 21:47:20.000000', 'Putri', '13000000', 'Cash', 'Eko Saputro', '100000', '0', '13100000', 'Paid', '2023-09-26 09:54:25', NULL);