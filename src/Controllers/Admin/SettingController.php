<?php

namespace Azuriom\Plugin\Shop\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Shop\Payment\Currencies;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display the shop settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('shop::admin.settings', [
            'currencies' => Currencies::all(),
            'currentCurrency' => setting('currency', 'USD'),
            'goal' => (int) setting('goal', 0),
        ]);
    }

    /**
     * Update the shop settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function save(Request $request)
    {
        Setting::updateSettings($this->validate($request, [
            'currency' => ['required', Rule::in(Currencies::codes())],
            'goal' => ['nullable', 'integer', 'min:0'],
        ]));

        Setting::updateSettings([
            'shop.use-site-money' => $request->has('use-site-money'),
            'shop.month-goal' => $request->input('goal'),
        ]);

        return redirect()->route('shop.admin.settings')
            ->with('success', trans('admin.settings.status.updated'));
    }
}
