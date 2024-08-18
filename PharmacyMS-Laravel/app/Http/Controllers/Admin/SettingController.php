<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use QCod\AppSettings\SavesSettings;
use QCod\AppSettings\Setting\AppSettings; // Import AppSettings

class SettingController extends Controller
{
    use SavesSettings;

    // Method to update the currency
    public function updateCurrency(Request $request, AppSettings $appSettings)
    {
        // Validate the incoming request data
        $request->validate([
            'app_currency' => 'required|max:10',
        ]);

        // Use the store method from the SavesSettings trait
        $this->store($request, $appSettings);

        // Redirect back with a success message
        return redirect()->route('settings')->with('success', 'Currency updated successfully.');
    }
}
