<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Services\ProgramService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgramController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private ProgramService $programService
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'is_active' => $request->input('is_active'),
            'sort' => $request->input('sort', 'order'),
        ];

        $programs = $this->programService->getPaginatedPrograms($filters, self::PER_PAGE);

        return Inertia::render('Admin/Programs/Index', [
            'programs' => $programs,
            'filters' => $request->only(['search', 'is_active', 'sort']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Programs/Create');
    }

    public function store(StoreProgramRequest $request)
    {
        $program = $this->programService->createProgram(
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.programs.index')
            ->with('success', __('Program created successfully.'));
    }

    public function show(Program $program)
    {
        $program = $this->programService->getProgramById($program->id);
        
        return Inertia::render('Admin/Programs/Show', [
            'program' => $program,
        ]);
    }

    public function edit(Program $program)
    {
        $program = $this->programService->getProgramById($program->id);
        
        return Inertia::render('Admin/Programs/Edit', [
            'program' => $program,
        ]);
    }

    public function update(UpdateProgramRequest $request, Program $program)
    {
        $this->programService->updateProgram(
            $program,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.programs.index')
            ->with('success', __('Program updated successfully.'));
    }

    public function destroy(Program $program)
    {
        $this->programService->deleteProgram($program);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', __('Program deleted successfully.'));
    }
}

