<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class NLPController extends Controller
{
    public function parse(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string',
        ]);

        $sentence = $request->input('sentence');
        $pythonScriptPath = base_path('parser.py');

        // Jalankan Python script
        $process = new Process(['python', $pythonScriptPath, $sentence]);
        $process->run();

        // Jika terjadi error, kembalikan JSON error
        if (!$process->isSuccessful()) {
            return response()->json([
                'error' => $process->getErrorOutput()
            ], 500);
        }

        // Ambil hasil output
        $output = $process->getOutput();
        $parsedData = json_decode($output, true);

        // Kembalikan JSON response
        return response()->json([
            'sentence' => $sentence,
            'morphemes' => $parsedData ?? []
        ]);
    }
}
