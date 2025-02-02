<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class NLPController extends Controller
{
    public function parse(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string',
        ]);

        $sentence = $request->input('sentence');
        $pythonScriptPath = base_path('parser.py');

        // Gunakan python3 jika perlu, atau pastikan python menunjuk ke interpreter yang tepat
        $process = new Process(['python3', $pythonScriptPath, $sentence]);
        $process->run();

        // Jika terjadi error, log error dan kembalikan response JSON error
        if (!$process->isSuccessful()) {
            Log::error("Error saat menjalankan parser.py: " . $process->getErrorOutput());
            return response()->json([
                'error' => $process->getErrorOutput()
            ], 500);
        }

        $output = $process->getOutput();
        $parsedData = json_decode($output, true);

        // Jika output tidak valid JSON, log outputnya juga
        if (is_null($parsedData)) {
            Log::error("Output dari parser.py tidak valid JSON: " . $output);
        }

        return response()->json([
            'sentence' => $sentence,
            'morphemes' => $parsedData ?? []
        ]);
    }
}
