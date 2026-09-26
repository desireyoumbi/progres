<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('author')->orderBy('meeting_date', 'desc')->paginate(10);
        return view('admin.reports.index', compact('reports'));
    }

    public function create()
    {
        return view('admin.reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'content' => 'required|string',
        ]);

        Report::create([
            'title' => $request->title,
            'meeting_date' => $request->meeting_date,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Rapport de réunion enregistré avec succès.');
    }

    public function show(Report $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        return view('admin.reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'content' => 'required|string',
        ]);

        $report->update([
            'title' => $request->title,
            'meeting_date' => $request->meeting_date,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Rapport mis à jour avec succès.');
    }

   

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('admin.reports.index')->with('success', 'Rapport supprimé avec succès.');
    }
}