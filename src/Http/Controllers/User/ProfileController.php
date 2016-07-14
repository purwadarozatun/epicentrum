<?php

namespace Laravolt\Epicentrum\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests;

class ProfileController extends UserController
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->repository->skipPresenter()->find($id);
        $profile = $user->profile;
        $timezones = $this->timezone->lists();
        return view('users::profile.edit', compact('user', 'profile', 'timezones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->repository->updateProfile($request->except('_token'), $id);
        \Krucas\Notification\Facades\Notification::success('Profil berhasil diperbarui');

        return redirect()->back();
    }

}
