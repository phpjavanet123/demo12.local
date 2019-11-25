<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;
use League\Csv\XMLConverter;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $parameters = $request->all();
        $parameters['export'] = 'csv';
        $exportFileUrl = new \StdClass();
        $exportFileUrl->csvUrl = route('transactions.index', $parameters);

        $parameters['export'] = 'xml';
        $exportFileUrl->xmlUrl = route('transactions.index', $parameters);

        $fromDate   = $request->get('date_from');
        $toDate     = $request->get('to_from');
        $fromDate   = $fromDate ? new Carbon($fromDate) : null;
        $toDate     = $toDate ? new Carbon($toDate) : null;

        $userID     = $request->get('user_id', null);
        $user       = $userID ? User::findOrFail($userID) : Auth::user();

        $transactions           = $user->wallet()->first()->transactionsFromTo($fromDate, $toDate);
        $transactionsSum        = $transactions->sum('from_wallet_currency_amount');
        $transactionsDefaultSum = $transactions->sum('default_currency_amount');

        $export = $request->get('export');
        if ($export) {
            $methodFactory = 'getReport' . strtoupper($export);
            return $this->{$methodFactory}($transactions, $transactionsSum, $transactionsDefaultSum);
        }

        $users = User::all();
        return view('transactions.index', compact(
            'users',
            'transactions',
            'transactionsSum',
            'transactionsDefaultSum',
            'exportFileUrl'
        ));
    }

    private function getReportXML($transactions, $transactionsSum, $transactionsDefaultSum)
    {
        //TODO::: move to App/Http/Response objects
        $converter = (new XMLConverter())
            ->rootElement('csv')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name');

        $csvExporter = $this->getCSVExporter($transactions, $transactionsSum, $transactionsDefaultSum);
        $dom = $converter->convert($csvExporter->getReader()->getIterator());
        $dom->formatOutput = true;
        $dom->encoding = 'iso-8859-15';

        $filename = date('Y-m-d_His') . '.xml';
        $csv      = Writer::createFromString($dom->saveXML());
        $csv->output($filename);
    }

    private function getReportCSV($transactions, $transactionsSum, $transactionsDefaultSum)
    {
        //TODO::: move to App/Http/Response objects
        $this->getCSVExporter($transactions, $transactionsSum, $transactionsDefaultSum)->download();
    }

    private function getCSVExporter($transactions, $transactionsSum, $transactionsDefaultSum)
    {
        $csvExporter = new \Laracsv\Export();
        $csvExporter->build($transactions, ['id', 'default_currency_amount', 'updated_at']);
        $csvWriter  = $csvExporter->getWriter();
        $csvRow     = [
            'title' => ['Total', 'Total Default'],
            'data' => [$transactionsSum, $transactionsDefaultSum],
        ];
        $csvWriter->insertOne($csvRow['title']);
        $csvWriter->insertOne($csvRow['data']);

        return $csvExporter;
    }
}
