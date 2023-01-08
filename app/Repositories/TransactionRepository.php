<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Invoices;
use App\Models\Transaction;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Foreach_;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Traits\NotificationTrait;

class TransactionRepository
{
    use CrudTrait, ResponseTraits, MainTrait, NotificationTrait;
    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * UserRepository constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all transactions with Role.
     *
     * @return Transaction $transaction
     */
    public function index()
    {
        return QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->select('id', 'invoice_id', 'date', 'paid_amount', 'status', 'reference_number')
            ->with(['invoice' => function ($q) {
                $q->select(['id', 'order_id', 'payment_method_id'])
                    ->with([
                        'paymentMethod:id,name',
                        'order' => function ($q) {
                            $q->select(['id', 'user_id'])
                                ->with(['user:id,name']);
                        }
                    ]);
            }])
            ->allowedFilters(['reference_number', 'invoice_id', 'date', 'invoice.order.user.name', 'paid_amount', 'status'])
            ->allowedSorts(['reference_number', 'invoice_id', 'date', 'invoice.order.user.name', 'paid_amount', 'status'])
            ->paginate(10);
    }
    /**
     * Get all transactions with Role.
     *
     * @return Transaction $transaction
     */
    public function invoices()
    {
        $Invoices = Invoices::orderBy('id', 'desc')->with(['order' => function ($q) {
            $q->select(['id', 'user_id'])->with('user:id,name');
        }])->get(['id', 'order_id']);
        $Invoice_arr = [];
        foreach ($Invoices->all() as $Invoice) {
            $Invoice['user'] = $Invoice->order->user->name;
            $Invoice = $Invoice->only('id', 'user');
            $Invoice_arr[] = $Invoice;
        }
        return $Invoice_arr;
    }

    /**
     *  Validate User And Provider data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function store($request)
    {
        $this->model->fill($request->all());
        $this->model->save();
        $Order= $this->model->invoice->order;
        $this->send(
            Auth::id(),
            [$Order->user_id],
            "معاملة جديدة",
            " تمت إضافة معاملة إلى طلبيتك بقيمه " . $this->model->paid_amount,
            $this->model->id,
            "transaction"
        );
        return $this->model;
    }
    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $transaction)
    {
        $transaction = Transaction::findOrFail($transaction);
        $transaction->status = $request->status;
        $transaction->save();
        $Order= $transaction->invoice->order;
        // dd($Order->user_id);
        $this->send(
            Auth::id(),
            [$Order->user_id],
            "تحديث المعاملة الخاصة",
            "تم تغيير حالة المعاملة الخاصة إلى ". trans('order.'. $transaction->status),
            $transaction->id,
            "transaction"
        );
        return $transaction;
    }

    /**
     * Return back the count for all the stores with each statue .
     *
     */
    public function statistics()
    {
        $transactions = $this->model
            ->select('id', 'status')->get();

        $under_review = $transactions->where('status', 'under_review')->count();
        $cancelled = $transactions->where('status', 'cancelled')->count();
        $confirmed = $transactions->where('status', 'confirmed')->count();
        return [
            'transactions' => $transactions->count(),
            'under_review' => ['count' => $under_review, 'percentage' => ($under_review / $transactions->count() * 100)],
            'cancelled' => ['count' => $cancelled, 'percentage' => ($cancelled / $transactions->count() * 100)],
            'confirmed' => ['count' => $confirmed, 'percentage' => ($confirmed / $transactions->count() * 100)],

        ];
    }
    public function report($request)
    {
        if ($request->has('store_id') && $request->store_id != null) {
            $Transactions= QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->select('id', 'invoice_id', 'date', 'paid_amount', 'status', 'reference_number')
            ->whereIn('status', $request->status)
            ->whereBetween('date', [Carbon::parse($request->from_date), Carbon::parse($request->to_date)])
            ->withAndWhereHas('invoice.order.store', function ($q) use($request){
                $q->where('id', $request->store_id)->select('id','name','phone');
             })
             ->with('invoice.order:id,store_id')
             ->with('invoice:id,order_id')
            ->allowedFilters(['reference_number', 'invoice_id', 'date', 'invoice.order.user.name', 'paid_amount', 'status'])
            ->allowedSorts(['reference_number', 'invoice_id', 'date', 'invoice.order.user.name', 'paid_amount', 'status'])
            ->paginate(10);
        } elseif ($request->has('user_id') && $request->user_id != null) {
            $Transactions= QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->select('id', 'invoice_id', 'date', 'paid_amount', 'status', 'reference_number')
            ->whereIn('status', $request->status)
            ->whereBetween('date', [Carbon::parse($request->from_date), Carbon::parse($request->to_date)])
            ->withAndWhereHas('invoice.order.user', function ($q) use($request){
                $q->where('id', $request->user_id)->select('id','name');
             })
             ->with('invoice.order.store:id,name')
             ->with('invoice.order:id,store_id,user_id')
             ->with('invoice:id,order_id')
            ->allowedFilters(['reference_number', 'invoice_id', 'date', 'invoice.order.user.name', 'paid_amount', 'status'])
            ->allowedSorts(['reference_number', 'invoice_id', 'date', 'invoice.order.user.name', 'paid_amount', 'status'])
            ->paginate(10);
        }
      
        $Transactions_total_amount = $Transactions->pluck('paid_amount')->sum();
        return['Transactions total amount'=>$Transactions_total_amount,'Transactions'=>$Transactions];
    }
}
