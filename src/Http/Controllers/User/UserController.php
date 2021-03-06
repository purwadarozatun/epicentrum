<?php

namespace Laravolt\Epicentrum\Http\Controllers\User;

use Illuminate\Routing\Controller;
use Laravolt\Acl\Models\Role;
use Laravolt\Epicentrum\Http\Requests\DeleteAccount;
use Laravolt\Epicentrum\Mail\AccountInformation;
use Laravolt\Epicentrum\Http\Requests\CreateAccount;
use Laravolt\Epicentrum\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Mail;
use Laravolt\Epicentrum\Repositories\TimezoneRepository;

class UserController extends Controller
{

    /**
     * @var UserRepositoryEloquent
     */
    protected $repository;

    /**
     * @var TimezoneRepositoryArray
     */
    protected $timezone;

    /**
     * UserController constructor.
     * @param UserRepositoryEloquent $repository
     * @param TimezoneRepositoryArray $timezone
     */
    public function __construct(RepositoryInterface $repository, TimezoneRepository $timezone)
    {
        $this->repository = $repository;
        $this->timezone = $timezone;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = $this->repository->scopeQuery(function ($query) {
            return $query->orderBy('name');
        })->skipPresenter()->paginate();
        return view('epicentrum::index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $statuses = $this->repository->availableStatus();
        $roles = Role::all()->pluck('name', 'id');
        $multipleRole = config('laravolt.epicentrum.role.multiple');

        return view('epicentrum::create', compact('statuses', 'roles', 'multipleRole'));
    }

    /**
     * Store the specified resource.
     *
     * @param CreateAccount $request
     * @return Response
     */
    public function store(CreateAccount $request)
    {
        // save to db
        $roles = $request->get('roles', []);
        $user = $this->repository->createByAdmin($request->only('name', 'email', 'password', 'status', 'must_change_password'), $roles);
        $password = $request->get('password');

        // send account info to email
        if ($request->has('send_account_information')) {
            Mail::to($user)->send(new AccountInformation($user, $password));
            //Mail::send('emails.account_information', compact('user', 'password'), function($message) use ($user) {
            //    $message->subject('Your Account Information');
            //    $message->to($user->email);
            //});
        }

        return redirect()->route('epicentrum::users.index')->withSuccess(trans('epicentrum::message.user_created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return redirect(route('epicentrum::account.edit', $id));
    }

    public function destroy(DeleteAccount $request, $id)
    {
        $this->repository->delete($id);

        return redirect(route('epicentrum::users.index'))->withSuccess(trans('epicentrum::message.user_deleted'));
    }
}
