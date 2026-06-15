<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function dashboard()
    {
        return view('teacher.dashboard');
    }

    public function resultats()
    {
        return view('teacher.resultats');
    }

    public function evolution()
    {
        return view('teacher.evolution');
    }

    public function commentaires()
    {
        return view('teacher.commentaires');
    }

    public function rapport()
    {
        return view('teacher.rapport');
    }

    public function exporterRapport(Request $request)
    {
        $request->validate([
            'periode_id' => 'required',
        ]);
        // TODO: generate PDF using DomPDF
        return redirect()->route('teacher.rapport')->with('success', 'Rapport PDF généré.');
    }
}
